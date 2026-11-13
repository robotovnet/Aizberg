<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Catalog\Http\Controllers\GroupController;
use Modules\Catalog\Http\Controllers\FeatureController;
use Modules\Catalog\Http\Controllers\SectionController;
use Modules\Catalog\Http\Controllers\FilterController;
use Modules\Catalog\Http\Controllers\ProductController;
use Modules\Catalog\Http\Controllers\BrandController;

Route::group([
    'prefix' => 'catalog',
    'as' => 'catalog.',
    'middleware' => ['ajax']
], function () {
    Route::get('products/{product_id}', [ProductController::class, 'show'])
        ->name('product.show');
});

Route::group([
    'prefix' => 'catalog',
    'as' => 'catalog.',
    'middleware' => ['ajax', 'auth:staff']
], function () {
    $group   = 'Modules\Catalog\Entities\Group';
    $feature = 'Modules\Catalog\Entities\Feature';
    $section = 'Modules\Catalog\Entities\Section';
    $filter  = 'Modules\Catalog\Entities\Filter';
    $product = 'Modules\Catalog\Entities\Product';
    $brand   = 'Modules\Catalog\Entities\Brand';

    // Groups
    Route::post('groups/store', [GroupController::class, 'store'])
        ->middleware('can:management,' . $group)
        ->name('group.store');

    Route::patch('groups/{catalog_group_id}', [GroupController::class, 'update'])
        ->middleware('can:management,' . $group)
        ->name('group.update');

    Route::delete('groups/{catalog_group_id}', [GroupController::class, 'destroy'])
        ->middleware('can:management,' . $group)
        ->name('group.delete');

    Route::get('groups/{catalog_group_id}', [GroupController::class, 'show'])
        //->middleware('can:view,' . $group)
        ->name('group.show');

    Route::get('groups/all', [GroupController::class, 'index'])
        //->middleware('can:view,' . $group)
        ->name('group.all');

    // Features
    Route::post('features/store', [FeatureController::class, 'store'])
        ->middleware('can:management,' . $feature)
        ->name('feature.store');

    Route::patch('features/{catalog_feature_id}', [FeatureController::class, 'update'])
        ->middleware('can:management,' . $feature)
        ->name('feature.update');

    Route::delete('features/{catalog_feature_id}', [FeatureController::class, 'destroy'])
        ->middleware('can:management,' . $feature)
        ->name('feature.delete');

    Route::get('features/{catalog_feature_id}', [FeatureController::class, 'show'])
        ->middleware('can:view,' . $feature)
        ->name('feature.show');

    Route::get('features/all', [FeatureController::class, 'index'])
        //->middleware('can:view,' . $feature)
        ->name('feature.all');

    Route::get('features/values/{catalog_feature_id}', [FeatureController::class, 'getValues'])
        ->middleware('can:view,' . $feature)
        ->name('feature.values');

    Route::post('features/migrate-values', [FeatureController::class, 'migrateValues'])
        ->middleware('can:management,' . $feature)
        ->name('feature.migrate_values');

    Route::post('features/products-by-value/{catalog_feature_id}', [FeatureController::class, 'getProductsByFeatureValue'])
        ->middleware('can:view,' . $feature)
        ->name('feature.products_by_value');

    // Sections
    Route::post('sections/store', [SectionController::class, 'store'])
        ->middleware('can:management,' . $section)
        ->name('section.store');

    Route::patch('sections/{catalog_section_id}', [SectionController::class, 'update'])
        ->middleware('can:management,' . $section)
        ->name('section.update');

    Route::delete('sections/{catalog_section_id}', [SectionController::class, 'destroy'])
        ->middleware('can:management,' . $section)
        ->name('section.delete');

    Route::get('sections/{catalog_section_id}', [SectionController::class, 'show'])
        ->middleware('can:view,' . $section)
        ->name('section.show');

    Route::get('sections/all', [SectionController::class, 'index'])
        //->middleware('can:view,' . $section)
        ->name('section.all');

    Route::post('sections/{catalog_section_id}/icon', [SectionController::class, 'iconUpload'])
        ->middleware('can:management,' . $section)
        ->name('sections.icon_upload');

    Route::post('sections/{catalog_section_id}/main-page', [SectionController::class, 'displayOnMainPage'])
        ->middleware('can:management,' . $section)
        ->name('sections.display_on_main_page');

    Route::post('sections/{catalog_section_id}/related', [SectionController::class, 'relatedProducts'])
        ->middleware('can:management,' . $section)
        ->name('sections.related_products');

    Route::post('sections/order', [SectionController::class, 'order'])
        ->middleware('can:management,' . $section)
        ->name('sections.order');

    Route::patch('sections/{catalog_section_id}/seo', [SectionController::class, 'seoData'])
        ->middleware('can:management,' . $section)
        ->name('sections.seo_data');

    Route::delete('sections/{catalog_section_id}/seo', [SectionController::class, 'seoDataDelete'])
        ->middleware('can:management,' . $section)
        ->name('sections.seo_data_delete');

    Route::post('sections/tiled-order', [SectionController::class, 'orderTiledCatalog'])
        ->middleware('can:management,' . $section)
        ->name('sections.tiled_order');

    // Filters
    Route::post('filters/store', [FilterController::class, 'store'])
        ->middleware('can:management,' . $filter)
        ->name('filter.store');

    Route::patch('filters/{catalog_filter_id}', [FilterController::class, 'update'])
        ->middleware('can:management,' . $filter)
        ->name('filter.update');

    Route::patch('filters/update-many', [FilterController::class, 'updateMany'])
        ->middleware('can:management,' . $filter)
        ->name('filter.update_many');

    Route::delete('filters/{catalog_filter_id}', [FilterController::class, 'destroy'])
        ->middleware('can:management,' . $filter)
        ->name('filter.delete');

    Route::delete('filters/by-group/{delete_group_id}', [FilterController::class, 'deleteAllFiltersByGroup'])
        ->middleware('can:management,' . $filter)
        ->name('filter.delete_by_group');

    Route::get('filters/{catalog_filter_id}', [FilterController::class, 'show'])
        ->middleware('can:view,' . $filter)
        ->name('filter.show');

    Route::get('filters/all', [FilterController::class, 'index'])
        ->middleware('can:view,' . $filter)
        ->name('filter.all');

    Route::get('filters/total_by_product_group', [FilterController::class, 'totalByProductGroup'])
        ->middleware('can:view,' . $filter)
        ->name('filter.total_by_product_group');

    // Products
    Route::post('products/checking-products-by-articles', [ProductController::class, 'getProductIdsByArticles'])
        ->middleware('can:view,' . $product)
        ->name('products.checking_products_by_articles');

    Route::get('products/excel/{section_id}', [ProductController::class, 'productsAllData'])
        ->middleware('can:view,' . $product)
        ->withoutMiddleware('ajax')
        ->name('productsAllData');

    Route::get('products/1c/{section_id}', [ProductController::class, 'productsAllData1C'])
        ->middleware('can:view,' . $product)
        ->withoutMiddleware('ajax')
        ->name('productsAllData1C');

    Route::post('products/store', [ProductController::class, 'store'])
        ->middleware('can:management,' . $product)
        ->name('product.store');

    Route::patch('products/{product_id}', [ProductController::class, 'update'])
        ->middleware('can:management,' . $product)
        ->name('product.update');

    Route::delete('products/{product_id}', [ProductController::class, 'destroy'])
        ->middleware('can:management,' . $product)
        ->name('product.delete');

    Route::get('products/all', [ProductController::class, 'index'])
        //->middleware('can:view,' . $product)
        ->name('product.all');

    Route::get('products/description/{article}', [ProductController::class, 'getProductDescriptionByArticle'])
        ->middleware('can:view,' . $product)
        ->name('product.description');

    Route::post('products/similar', [ProductController::class, 'getSimilarProducts'])
        ->middleware('can:view,' . $product)
        ->name('product.similar');

    Route::get('products/similar/add/{pid}/{sid}', [ProductController::class, 'similarProductsAdd'])
        ->middleware('can:management,' . $product)
        ->name('product.similar_add');

    Route::get('products/similar/delete/{pid}/{sid}', [ProductController::class, 'similarProductsDelete'])
        ->middleware('can:management,' . $product)
        ->name('product.similar_delete');

    Route::post('products/photo/{uuid}/{order?}/{main?}', [ProductController::class, 'photoUpload'])
        ->middleware('can:management,' . $product)
        ->name('photo.upload');

    // Скачать все фотографии товара
    Route::get('products/photos/download/{product_id}', [ProductController::class, 'downloadPhotos'])
        ->middleware('can:view,' . $product)
        ->withoutMiddleware('ajax')
        ->name('products.photos.download');

    // ZIP: оригинальные фото по списку артикулов из Excel
    Route::post('products/photos/download-by-articles-excel', [ProductController::class, 'downloadPhotosByArticlesExcel'])
        ->middleware('can:view,' . $product)
        ->withoutMiddleware('ajax')
        ->name('products.photos.download_by_articles_excel');

    Route::post('products/photos/order', [ProductController::class, 'photoOrder'])
        ->middleware('can:management,' . $product)
        ->name('photo.order');

    Route::delete('products/photo/{photo_id}', [ProductController::class, 'photoDelete'])
        ->middleware('can:management,' . $product)
        ->name('photo.delete');

    Route::post('products/ad/photo', [ProductController::class, 'adPhotoUpload'])
        ->middleware('can:management,' . $product)
        ->name('ad_photo.upload');

    Route::get('products/total/{section_id?}', [ProductController::class, 'getTotalProducts'])
        ->middleware('can:view,' . $product)
        ->name('product.total_products');

    Route::get('products/toggle_visibility/{pid}', [ProductController::class, 'toggleVisibilityOfProduct'])
        ->middleware('can:management,' . $product)
        ->name('product.toggle_visibility_product');

    // Brands
    Route::post('brands/store', [BrandController::class, 'store'])
        ->middleware('can:management,' . $brand)
        ->name('brands.store');

    Route::patch('brands/{brand_id}', [BrandController::class, 'update'])
        ->middleware('can:management,' . $brand)
        ->name('brands.update');

    Route::delete('brands/{brand_id}', [BrandController::class, 'destroy'])
        ->middleware('can:management,' . $brand)
        ->name('brands.delete');

    Route::get('brands/{brand_id}', [BrandController::class, 'show'])
        ->middleware('can:view,' . $brand)
        ->name('brands.show');

    Route::get('brands/all', [BrandController::class, 'index'])
        //->middleware('can:view,' . $brand)
        ->name('brands.all');
});
