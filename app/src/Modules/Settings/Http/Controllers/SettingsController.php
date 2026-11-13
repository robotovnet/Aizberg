<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Repositories\SettingsRepository;
use Modules\Settings\Http\Requests\UpdateSettingsRequest;

/**
 * Class SettingsController
 * @package Modules\Settings\Http\Controllers
 */
class SettingsController extends Controller
{
    /** @var SettingsRepository An instance of the Dashboard Menu Repository. */
    private SettingsRepository $settingsRepo;

    /**
     * SettingsController constructor.
     */
    public function __construct(SettingsRepository $settingsRepo)
    {
        $this->settingsRepo = $settingsRepo;
    }

    /**
     * Returns all settings.
     */
    public function index(Request $request): JsonResponse
    {
        $code = $request->input('code', null);

        return JsonResponse::successful($this->settingsRepo->all($code));
    }

    /**
     * Updates a specific setting.
     */
    public function update(UpdateSettingsRequest $request, SettingsRepository $settingsRepo): JsonResponse
    {
        $settingsRepo->update($request->only(['value']));

        return JsonResponse::successful();
    }

    /**
     * Returns settings by code.
     */
    public function getByCode(SettingsRepository $settingsRepo, string $settingCode): JsonResponse
    {
        $data = $settingsRepo->getByCode($settingCode);

        return JsonResponse::successful($data);
    }
}
