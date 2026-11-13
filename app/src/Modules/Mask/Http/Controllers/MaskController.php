<?php

declare(strict_types=1);

namespace Modules\Mask\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Mask\Repositories\MaskRepository;
use Illuminate\Support\Facades\Response;

/**
 * Class MaskController
 * @package Modules\Mask\Http\Controllers
 */
class MaskController extends Controller
{
    /** @var MaskRepository An instance of the Mask Repository. */
    private MaskRepository $maskRepository;

    /**
     * MaskController constructor.
     */
    public function __construct(MaskRepository $maskRepository)
    {
        $this->maskRepository = $maskRepository;
    }

    /**
     * Returns all phones masks.
     */
    public function getAllPhonesMasks(): JsonResponse
    {
        $masks = $this->maskRepository->getAll('phone');

        return Response::json($masks, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
