<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\ImageUploader\Entities\ImageUpload;
use Modules\ImageUploader\Http\Controllers\ImageUploaderController;

Route::group([
    'prefix' => 'image-uploader',
    'as' => 'imageuploader.',
    'middleware' => ['ajax', 'auth:staff'],
], static function (): void {
    $image = ImageUpload::class;

    Route::get('/', [ImageUploaderController::class, 'all'])
        ->middleware('can:view,' . $image)
        ->name('all');

    Route::get('categories', [ImageUploaderController::class, 'categories'])
        ->middleware('can:view,' . $image)
        ->name('categories');

    Route::post('/', [ImageUploaderController::class, 'upload'])
        ->middleware('can:management,' . $image)
        ->name('upload');

    Route::get('{image}', [ImageUploaderController::class, 'data'])
        ->middleware('can:view,' . $image)
        ->name('data');

    Route::put('{image}', [ImageUploaderController::class, 'update'])
        ->middleware('can:management,' . $image)
        ->name('update');

    Route::patch('{image}', [ImageUploaderController::class, 'update'])
        ->middleware('can:management,' . $image);

    Route::delete('{image}', [ImageUploaderController::class, 'delete'])
        ->middleware('can:management,' . $image)
        ->name('delete');
});

