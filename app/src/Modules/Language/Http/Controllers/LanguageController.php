<?php

declare(strict_types=1);

namespace Modules\Language\Http\Controllers;

use App\Helpers\LangPhrases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Modules\Language\Services\LanguageService;

/**
 * Class LanguageController
 * @package Modules\Language\Http\Controllers
 */
class LanguageController extends Controller
{
    /** @var LanguageService Service layer for working with languages */
    private LanguageService $language;

    /**
     * LanguageController constructor.
     */
    public function __construct(LanguageService $language)
    {
        $this->language = $language;
    }

    /**
     * Set cookie with the selected localization.
     */
    public function setLanguage(Request $request): JsonResponse
    {
        $lang = $request->input($this->language->getLanguageCookieName());

        if (is_null($lang) || !$this->language->canSupport($lang)) {
            $lang = $this->language->getDefault();
        }

        $this->language->makeActive($lang);

        return Response::json([ 'message' => LangPhrases::SUCCESS_MESSAGE ]);
    }
}
