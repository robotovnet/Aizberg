<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Catalog\Entities\ProductPhoto;
use Modules\Catalog\Http\Requests\AdPhotoUploadRequest;
use Modules\Catalog\Http\Requests\CreateOrUpdateProductRequest;
use Modules\Catalog\Http\Requests\GetSimilarProductRequest;
use Modules\Catalog\Http\Requests\GetProductIdsByArticlesRequest;
use Modules\Catalog\Http\Requests\PhotoOrderRequest;
use Modules\Catalog\Http\Requests\PhotoUploadRequest;
use Modules\Catalog\Repositories\ProductPhotoRepository;
use Modules\Catalog\Repositories\ProductRepository;
use Modules\Catalog\Services\AdPhotoService;
use Modules\Catalog\Services\PhotoService;
use Modules\Catalog\Services\ExcelService;
use Modules\Catalog\Services\ProductPhotosByArticlesExcelService;
use Modules\Catalog\Http\Requests\ArticlesExcelPhotosZipRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Settings\Entities\Settings;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\Catalog\Entities\Product;

/**
 * Class ProductController
 * @package Modules\Catalog\Http\Controllers
 */
class ProductController extends Controller
{
    /** @var ProductRepository An instance of the Product Repository. */
    private ProductRepository $productRepo;

    /**
     * ProductController constructor.
     */
    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    /**
     * Returns all products.
     */
    public function index(Request $request): JsonResponse
    {
        $search     = $request->input('search', '');
        $page       = $request->input('page', 1);
        $onPage     = $request->input('onPage', Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value']);
        $sectionId  = $request->input('section_id', 0);
        $sorting    = $request->input('sorting', '');
        $direction  = $request->input('direction', 'asc');
        $filters    = $request->input('filters', []);

        return JsonResponse::successful(
            $this->productRepo->all($search, (int)$page, (int)$sectionId, $sorting, $direction, $filters, (int)$onPage)
        );
    }

    /**
     * Creates a new product.
     */
    public function store(CreateOrUpdateProductRequest $request): JsonResponse
    {
        $request->merge([
            'name'          => Str::getNamesJson($request->names),
            'overview_text' => Str::getNamesJson($request->overview_texts),
        ]);

        $productId = $this->productRepo->create($request->all());

        return JsonResponse::successful(
            [
                'id' => $productId,
                'message' => 'success'
            ]
        );
    }

    /**
     * Returns a list of similar products.
     */
    public function getSimilarProducts(GetSimilarProductRequest $request, ProductRepository $productRepo): JsonResponse
    {
        return JsonResponse::successful($productRepo->getSimilarProducts($request->products));
    }

    public function toggleVisibilityOfProduct(ProductRepository $productRepo, int $pid): JsonResponse
    {
        $productRepo->toggleVisibilityOfProduct($pid);

        return JsonResponse::successful();
    }


    public function similarProductsAdd(ProductRepository $productRepo, int $pid, int $sid): JsonResponse
    {
        //$sids = explode(',', $sid);

        //foreach ($sids as $oneSid) {
        //    $productRepo->addSimilarProduct($pid, (int)$oneSid);
        //}

        $productRepo->addSimilarProduct($pid, $sid);

        return JsonResponse::successful();
    }

    public function similarProductsDelete(ProductRepository $productRepo, int $pid, int $sid): JsonResponse
    {
        $productRepo->deleteSimilarProduct($pid, $sid);

        return JsonResponse::successful();
    }

    /**
     * Returns product data by id.
     */
    public function show(ProductRepository $productRepo): JsonResponse
    {
        return JsonResponse::successful($productRepo->data());
    }

    /**
     * Updates product data.
     */
    public function update(
        CreateOrUpdateProductRequest $request,
        ProductRepository $productRepo
    ): JsonResponse {
        $request->merge([
            'name'          => Str::getNamesJson($request->names),
            'overview_text' => Str::getNamesJson($request->overview_texts),
        ]);

        $productRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the product.
     */
    public function destroy(ProductRepository $productRepo): JsonResponse
    {
        $productRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Uploading product photo.
     */
    public function photoUpload(
        PhotoUploadRequest $request,
        PhotoService $photo,
        ProductPhotoRepository $photoRepo,
        string $uuid,
        int $order = 1,
        string $main = 'not_main'
    ): JsonResponse {
        $uploadedFile = $request->file('photo');
        $realPath = $uploadedFile->getRealPath();
        $contentHash = $realPath !== false ? hash_file('sha256', $realPath) : false;
        if ($contentHash === false) {
            return Response::json(
                ['message' => 'Не удалось вычислить хеш загружаемого файла.', 'code' => 'photo_hash_failed'],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        if ($photoRepo->hasActiveDuplicateByUuidAndContentHash($uuid, $contentHash)) {
            return Response::json(
                [
                    'message' => 'Фотография с таким же содержимым уже загружена для этого товара.',
                    'code' => 'duplicate_photo',
                ],
                409,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        $photo->save();
        $photoPath = $photo->getPathToFile();
        $photoId = $photoRepo->create($photoPath, $uuid, $order, $main, $contentHash);

        return JsonResponse::successful([ 'photo_path' => $photoPath, 'photo_id' => $photoId ]);
    }

    /**
     * Deletes product photo.
     */
    public function photoDelete(ProductPhotoRepository $photoRepo, int $photoId = 0): JsonResponse
    {
        $photoRepo->delete($photoId);

        return JsonResponse::successful();
    }

    /**
     * Uploading product photo for ads.
     */
    public function adPhotoUpload(AdPhotoUploadRequest $request, AdPhotoService $photo): JsonResponse
    {
        $photo->save();
        $photoPath = $photo->getPathToFile();

        return JsonResponse::successful([ 'photo_path' => $photoPath ]);
    }

    public function photoOrder(PhotoOrderRequest $request): JsonResponse
    {
        $photosOrder = $request->input('photos');
        $photoIds = array_column($photosOrder, 'photo_id');
        $uuid = ProductPhoto::whereIn('id', $photoIds)->value('uuid');
        if ($uuid !== null) {
            // Сброс в том числе у soft-deleted, иначе «главная» на удалённой строке мешает назначить Y активной
            ProductPhoto::withTrashed()->where('uuid', $uuid)->update(['is_main' => 'N']);
        }

        foreach ($photosOrder as $item) {
            ProductPhoto::where('id', $item['photo_id'])
                ->update(['is_main' => $item['is_main'], 'order' => $item['pos']]);
        }

        return JsonResponse::successful();
    }

    public function productsAllData(int $sectionId)
    {
        $excelService = new ExcelService();

        return $excelService->productsAllData($sectionId);
    }

    public function productsAllData1C(int $sectionId)
    {
        $excelService = new ExcelService();

        return $excelService->productsAllData1C($sectionId);
    }

    /**
     * Get total products.
     */
    public function getTotalProducts(ProductRepository $productRepo, Request $request, int $section_id = 0): JsonResponse
    {
        $search     = $request->input('search', '');
        $onPage     = $request->input('onPage', Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value']);
        $sorting    = $request->input('sorting', '');
        $direction  = $request->input('direction', 'asc');
        $filters    = $request->input('filters', []);
        $sectionId  = $section_id;

        $total = $this->productRepo->all($search, 1, (int)$sectionId, $sorting, $direction, $filters, 0);

        return JsonResponse::successful([
            'total_products' => $total,
            'per_page'       => $onPage,
            'total_page'     => ceil($total / $onPage),
        ]);
    }

    public function downloadPhotos(ProductPhotoRepository $productPhotoRepo, ProductRepository $productRepo): BinaryFileResponse
    {
        return $productPhotoRepo->downloadPhotos($productRepo->data()['id']);
    }

    /**
     * ZIP с оригинальными фото товаров по артикулам из Excel (столбец A до пустой строки).
     */
    public function downloadPhotosByArticlesExcel(
        ArticlesExcelPhotosZipRequest $request,
        ProductPhotosByArticlesExcelService $service
    ): BinaryFileResponse|JsonResponse {
        try {
            return $service->buildZipResponse($request->file('excel'));
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Получает описание товара по артикулу.
     *
     * @param string $article Артикул товара
     * @return JsonResponse
     */
    public function getProductDescriptionByArticle(string $article): JsonResponse
    {
        $description = $this->productRepo->getDescriptionByArticle($article);
        
        return JsonResponse::successful(['description' => $description]);
    }

    /**
     * Получает id товаров по массиву артикулов.
     * 
     * @param GetProductIdsByArticlesRequest $request
     * @return JsonResponse
     */
    public function getProductIdsByArticles(GetProductIdsByArticlesRequest $request): JsonResponse
    {
        $articles = $request->input('articles', []);

        // Фильтруем пустые значения для запроса к БД
        $filteredArticles = array_filter($articles, function($article) {
            return !empty($article) && $article !== null;
        });

        // Получаем товары по артикулам (только для непустых артикулов)
        $products = collect();
        if (!empty($filteredArticles)) {
            $products = Product::whereIn('vendor_code', $filteredArticles)
                ->select('id', 'vendor_code')
                ->get()
                ->keyBy('vendor_code');
        }

        // Формируем результат: для каждого артикула возвращаем id или null
        $result = [];
        foreach ($articles as $article) {
            if (empty($article) || $article === null) {
                $result[$article] = null;
            } else {
                $result[$article] = $products->has($article) ? $products[$article]->id : null;
            }
        }

        return JsonResponse::successful($result);
    }
}
