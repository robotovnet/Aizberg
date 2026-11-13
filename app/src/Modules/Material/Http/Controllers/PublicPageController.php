<?php

declare(strict_types=1);

namespace Modules\Material\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Modules\Material\Repositories\MaterialRepository;

/**
 * Публичные данные страниц (материалы с type = page) для сайта.
 */
class PublicPageController extends Controller
{
    /** Магазин по умолчанию (используется, если shop_id не передан). */
    private const DEFAULT_SHOP_ID = 1;

    private MaterialRepository $materialRepo;

    public function __construct(MaterialRepository $materialRepo)
    {
        $this->materialRepo = $materialRepo;
    }

    /**
     * Returns an active page by slug. Falls back to the default shop if the page is missing in the requested one.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'shop_id' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return JsonResponse::failed(['errors' => $validator->errors()->toArray()]);
        }

        $shopId = (int) ($request->query('shop_id') ?? self::DEFAULT_SHOP_ID);

        $page = $this->materialRepo->findPage($shopId, $slug);

        if (!$page && $shopId !== self::DEFAULT_SHOP_ID) {
            $page = $this->materialRepo->findPage(self::DEFAULT_SHOP_ID, $slug);
        }

        if (!$page) {
            return Response::json(['message' => 'Page not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return JsonResponse::successful($page);
    }
}
