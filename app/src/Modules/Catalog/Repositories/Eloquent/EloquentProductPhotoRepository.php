<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\ProductPhoto;
use Modules\Catalog\Repositories\ProductPhotoRepository;
use Modules\Catalog\Support\ProductPhotoOriginPath;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class EloquentProductPhotoRepository
 * @package Modules\Catalog\Repositories\Eloquent
 */
class EloquentProductPhotoRepository implements ProductPhotoRepository
{
    /** @var Model An instance of the Eloquent Model. */
    protected Model $model;

    /**
     * Repository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function create(string $path, string $uuid, int $order, string $main, string $contentHash): int
    {
        $sizes = getimagesize(storage_path('app/public') . str_replace('/storage/', '/', $path));

        if ($main == 'main') {
            ProductPhoto::withTrashed()->where('uuid', $uuid)->update(['is_main' => 'N']);
        }

        $this->model = $this->model->create([
            'file_path'      => $path,
            'file_name'      => pathinfo($path)['filename'],
            'file_extension' => pathinfo($path)['extension'],
            'file_width'     => $sizes[0],
            'file_height'    => $sizes[1],
            'uuid'           => $uuid,
            'content_hash'   => $contentHash,
            'is_main'        => ($main == 'main') ? 'Y' : 'N',
            'order'          => $order,
        ]);

        Product::where('uuid', $uuid)->update(['has_photos' => 'Y']);

        return $this->model->id;
    }

    public function hasActiveDuplicateByUuidAndContentHash(string $uuid, string $contentHash): bool
    {
        if (ProductPhoto::query()
            ->where('uuid', $uuid)
            ->where('content_hash', $contentHash)
            ->exists()) {
            return true;
        }

        return ProductPhoto::query()
            ->where('uuid', $uuid)
            ->whereNull('content_hash')
            ->cursor()
            ->contains(function (ProductPhoto $photo) use ($contentHash): bool {
                $absolute = ProductPhotoOriginPath::absoluteOriginFromStoredPath($photo->file_path);
                if (! is_readable($absolute)) {
                    return false;
                }
                $fileHash = hash_file('sha256', $absolute);

                return $fileHash !== false && hash_equals($contentHash, $fileHash);
            });
    }

    /**
     * @inheritDoc
     */
    public function delete(int $photoId): self
    {
        $photo = ProductPhoto::find($photoId);
        if ($photo) {
            $photo->is_main = 'N';
            $photo->save();
        }
        $this->model->where('id', $photoId)->delete();

        if ($photo) {
            $hasPhotos = (ProductPhoto::where('uuid', $photo->uuid)->count() == 0) ? 'N' : 'Y';
            Product::where('uuid', $photo->uuid)->update(['has_photos' => $hasPhotos]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function downloadPhotos(int $productId): BinaryFileResponse
    {
        $product = Product::find($productId);
        $productPhotos = ProductPhoto::where('uuid', $product->uuid)->get();

        //$productName = json_decode($product['name'], true)[App::getLocale()];
        //$productName = Str::slug($productName);
        //$productName = preg_replace('/[^a-zA-Z0-9_-]/', '', $productName);

        $zip = new \ZipArchive();
        //$zipFileName = $productName . '.zip';
        $zipFileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $product->vendor_code) . '.zip';
        $zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($productPhotos as $photo) {
            $filePath = str_replace("/micro/", "/origin/", $photo->file_path);
            $zip->addFile(storage_path('app/public') . str_replace('/storage/', '/', $filePath), $photo->file_name . '.' . $photo->file_extension);
        }

        $zip->close();

        return response()->download($zipFileName);
    }
}
