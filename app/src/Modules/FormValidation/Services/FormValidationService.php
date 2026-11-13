<?php

declare(strict_types=1);

namespace Modules\FormValidation\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Modules\Language\Services\LanguageService;
use Modules\Mask\Repositories\MaskRepository;

/**
 * Class FormValidationService
 * @package Modules\FormValidation\Services
 */
class FormValidationService
{
    /** @var string Stores the name of the request class. */
    private string $className;

    /** @var object Stores an instance of the request class. */
    private object $instanceRequestClass;

    /** @var array Stores the names of excluded rules. */
    private array $excludeRules = [
        'exists:',
        'unique:',
        'captcha',
        'distinct',
        'timezone',
        'nullable',
        'date_format',
        'json',
        'uuid',
        'gt:minimum_order_value',
    ];

    /** @var array Stores the names of attributes that need a phone mask. */
    private array $attributesPhoneMask = ['phone'];

    /**
     * Returns the name of the request class.
     */
    public function setClassName(string $entity, string $request): self
    {
        $this->className = 'Modules\\' . ucfirst($entity) . '\Http\Requests\\' . ucfirst($request) . 'Request';

        return $this;
    }

    /**
     * Checks whether the class exists.
     */
    public function isClassExist(): bool
    {
        if (class_exists($this->className)) {
            $this->instanceRequestClass = new $this->className();

            return true;
        }

        return false;
    }

    /**
     * Returns the rules.
     */
    public function getRules(): array
    {
        $allRules = [];

        foreach ($this->instanceRequestClass->rules() as $attribute => $rulesLine) {
            if (is_string($rulesLine)) {
                $rules = explode('|', $rulesLine);
            } elseif (is_array($rulesLine)) {
                $rules = [];

                foreach ($rulesLine as $ruleItem) {
                    if (is_string($ruleItem)) {
                        $rules[] = $ruleItem;
                        continue;
                    }

                    if (is_object($ruleItem)) {
                        if (method_exists($ruleItem, '__toString')) {
                            $rules[] = (string) $ruleItem;
                            continue;
                        }

                        $rules[] = get_class($ruleItem);
                        continue;
                    }

                    $rules[] = (string) $ruleItem;
                }
            } else {
                $rules = [(string) $rulesLine];
            }

            foreach ($rules as $key => $rule) {
                foreach ($this->excludeRules as $excludeRule) {
                    if (Str::of($rule)->contains($excludeRule)) {
                        unset($rules[$key]);
                    }
                }
            }

            $allRules[$attribute] = implode('|', $rules);
        }

        return $allRules;
    }

    /**
     * Returns the messages.
     */
    public function getMessages(): array
    {
        $messages = [];

        // Casts key to 'validator.attribute' for js-library "mikeerickson/validatorjs"
        foreach ($this->instanceRequestClass->messages() as $key => $msg) {
            if (str_contains($key, '*') === true) {
                $messages[$key] = $msg;
                continue;
            }

            list($attribute, $rule) = explode('.', $key, 2);

            if (!in_array($rule, $this->excludeRules)) {
                $messages[$rule . '.' . $attribute] = $msg;
            }
        }

        return $messages;
    }

    /**
     * Returns the attributes.
     */
    public function getAttributes(): array
    {
        return $this->instanceRequestClass->attributes();
    }

    /**
     * Returns the language code.
     */
    public function getLanguageCode(): string
    {
        $languageService = App::make(LanguageService::class);

        return $languageService->getDefault();
    }

    /**
     * Returns the phone mask.
     */
    public function getPhoneMask(): array|null
    {
        $languageService = App::make(LanguageService::class);
        $maskRepo = App::make(MaskRepository::class);

        foreach ($this->instanceRequestClass->messages() as $key => $msg) {
            list($attribute) = explode('.', $key);

            if (in_array($attribute, $this->attributesPhoneMask)) {
                // TODO If client authorized, then get the code from the "client_country" database
                $activeLang = $languageService->retrieveActive();

                return $maskRepo->getByCode('phone', $activeLang);
            }
        }

        return null;
    }
}
