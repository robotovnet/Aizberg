<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Catalog\Http\Requests\CreateOrUpdateSectionRequest;
use Modules\Catalog\Http\Requests\DisplayOnMainPageRequest;
use Modules\Catalog\Http\Requests\OrderRequest;
use Modules\Catalog\Http\Requests\OrderTiledCatalogRequest;
use Modules\Catalog\Http\Requests\RelatedProductsRequest;
use Modules\Catalog\Http\Requests\SeoDataSectionRequest;
use Modules\Catalog\Repositories\SectionRepository;
use Modules\Catalog\Http\Requests\SectionIconUploadRequest;
use Modules\Catalog\Services\SectionIconService;
use Modules\Settings\Entities\Settings;
use Illuminate\Http\Request;

/**
 * Class SectionController
 * @package Modules\Catalog\Http\Controllers
 */
class SectionController extends Controller
{
    /** @var SectionRepository An instance of the Section Repository. */
    private SectionRepository $sectionRepo;

    /**
     * SectionController constructor.
     */
    public function __construct(SectionRepository $sectionRepo)
    {
        $this->sectionRepo = $sectionRepo;
    }

    /**
     * Returns all sections.
     */
    public function index(Request $request): JsonResponse
    {
        $model = $request->input('model', '');
        $orderField = $request->input('orderField', 'order');

        $needLevel = true;
        $withoutRootSections = false;

        if ($model == 'product') {
            $canPlaceProductsInRootSections = Settings::where('code', 'PLACE_PRODUCTS_ROOT_SECTIONS')->first()->toArray()['value'];
            $withoutRootSections = ($canPlaceProductsInRootSections === 'yes') ? false : true;
        }

        return JsonResponse::successful($this->sectionRepo->all($needLevel, $withoutRootSections, $orderField));
    }

    /**
     * Creates a new section.
     */
    public function store(CreateOrUpdateSectionRequest $request): JsonResponse
    {
        $request->merge([
            'name'   => Str::getNamesJson($request->names),
        ]);

        $sectionId = $this->sectionRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $sectionId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns section data by id.
     */
    public function show(SectionRepository $sectionRepo): JsonResponse
    {
        return JsonResponse::successful($sectionRepo->data());
    }

    /**
     * Updates section data.
     */
    public function update(
        CreateOrUpdateSectionRequest $request,
        SectionRepository $sectionRepo
    ): JsonResponse {
        $request->merge([
            'name'   => Str::getNamesJson($request->names),
        ]);

        $sectionRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the section.
     */
    public function destroy(SectionRepository $sectionRepo): JsonResponse
    {
        $sectionRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Uploading section icon.
     */
    public function iconUpload(
        SectionIconUploadRequest $request,
        SectionRepository $sectionRepo,
        SectionIconService $icon
    ): JsonResponse {

        $icon->save();
        $iconPath = $icon->getPathToFile();

        $sectionRepo->update(['thumbnail' => Str::of($iconPath)->basename()]);

        return JsonResponse::successful([ 'icon_path' => $iconPath ]);
    }

    public function displayOnMainPage(DisplayOnMainPageRequest $request, SectionRepository $sectionRepo): JsonResponse
    {
        $sectionRepo->displayOnMainPage($request->input('should_display', 'N'));

        return JsonResponse::successful();
    }

    public function relatedProducts(RelatedProductsRequest $request, SectionRepository $sectionRepo): JsonResponse
    {
        $sectionRepo->relatedProducts($request->input('products'));

        return JsonResponse::successful();
    }

    public function order(OrderRequest $request, SectionRepository $sectionRepo): JsonResponse
    {
        $sectionRepo->order($request->input('order'));

        return JsonResponse::successful();
    }

    public function seoData(SeoDataSectionRequest $request, SectionRepository $sectionRepo): JsonResponse
    {
        $sectionRepo->seoData($request->input('seo'));

        return JsonResponse::successful();
    }

    public function seoDataDelete(SectionRepository $sectionRepo): JsonResponse
    {
        $sectionRepo->seoDataDelete();

        return JsonResponse::successful();
    }

    /**
     * Sets the order of tiled on main page.
     */
    public function orderTiledCatalog(
        OrderTiledCatalogRequest $request,
        SectionRepository $sectionRepo
    ): JsonResponse {
        $sectionRepo->orderTiledCatalog($request->orders);

        return JsonResponse::successful();
    }
}
