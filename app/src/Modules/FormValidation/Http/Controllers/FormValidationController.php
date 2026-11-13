<?php

declare(strict_types=1);

namespace Modules\FormValidation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\FormValidation\Services\FormValidationService;

/**
 * Class FormValidationController
 * @package Modules\FormValidation\Http\Controllers
 */
class FormValidationController extends Controller
{
    /** @var FormValidationService An instance of the Form Validation Service */
    private FormValidationService $formValidationService;

    /**
     * FormValidationController constructor.
     */
    public function __construct(FormValidationService $formValidationService)
    {
        $this->formValidationService = $formValidationService;
    }

    /**
     * Returns validation rules, validation messages, phone mask and request attributes.
     */
    public function index($entity, $request): JsonResponse
    {
        if ($this->formValidationService->setClassName($entity, $request)->isClassExist()) {
            $data = [
                'rules'      => $this->formValidationService->getRules(),
                'messages'   => $this->formValidationService->getMessages(),
                'attributes' => $this->formValidationService->getAttributes(),
                'language'   => $this->formValidationService->getLanguageCode(),
            ];

            if ($mask = $this->formValidationService->getPhoneMask()) {
                $data['masks'] = $mask;
            }

            return JsonResponse::successful($data);
        }

        return JsonResponse::failed();
    }

    /**
     * Returns validation rules for profile page
     */
    public function profile(): JsonResponse
    {
        $data = [];
        $entity = 'staff';

        $requests = [
            'ProfileUpdateEmail',
            'ProfileUpdateName',
            'ProfileUpdatePassword',
            'ProfileUpdatePhone',
            'ProfileUpdateUserpic'
        ];

        for ($req = 0; $req < count($requests); $req++) {
            $this->formValidationService->setClassName($entity, $requests[$req])->isClassExist();

            $data[$requests[$req]] = [
                'rules'      => $this->formValidationService->getRules(),
                'messages'   => $this->formValidationService->getMessages(),
                'attributes' => $this->formValidationService->getAttributes(),
                'language'   => $this->formValidationService->getLanguageCode(),
            ];

            if ($mask = $this->formValidationService->getPhoneMask()) {
                $data[$requests[$req]]['masks'] = $mask;
            }
        }

        return JsonResponse::successful($data);
    }

    /**
     * Returns validation rules for staff profile page
     */
    public function staffProfile(): JsonResponse
    {
        $data = [];
        $entity = 'staff';

        $requests = [
            'staffUpdate',
            'staffSetPassword',
            'staffUploadUserpic',
        ];

        for ($req = 0; $req < count($requests); $req++) {
            $this->formValidationService->setClassName($entity, $requests[$req])->isClassExist();

            $data[$requests[$req]] = [
                'rules'      => $this->formValidationService->getRules(),
                'messages'   => $this->formValidationService->getMessages(),
                'attributes' => $this->formValidationService->getAttributes(),
                'language'   => $this->formValidationService->getLanguageCode(),
            ];

            if ($mask = $this->formValidationService->getPhoneMask()) {
                $data[$requests[$req]]['masks'] = $mask;
            }
        }

        return JsonResponse::successful($data);
    }

    /**
     * Returns validation rules for transport company create/update page
     */
    public function transportCompany(): JsonResponse
    {
        $data = [];
        $entity = 'transportCompany';

        $requests = [
            'createOrUpdateTransportCompany',
            // 'iconUpload',
        ];

        for ($req = 0; $req < count($requests); $req++) {
            $this->formValidationService->setClassName($entity, $requests[$req])->isClassExist();

            $data[$requests[$req]] = [
                'rules'      => $this->formValidationService->getRules(),
                'messages'   => $this->formValidationService->getMessages(),
                'attributes' => $this->formValidationService->getAttributes(),
                'language'   => $this->formValidationService->getLanguageCode(),
            ];
        }

        return JsonResponse::successful($data);
    }
}
