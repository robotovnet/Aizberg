<?php

declare(strict_types=1);

namespace Modules\Catalog\Services;

use Illuminate\Http\UploadedFile;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\ProductPhoto;
use Shuchkin\SimpleXLSX;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/**
 * Сбор оригинальных фотографий товаров по списку артикулов из Excel (столбец A до пустой строки).
 */
class ProductPhotosByArticlesExcelService
{
    /**
     * @return list<string>
     */
    public function parseArticlesFromExcel(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if ($path === false) {
            throw new \InvalidArgumentException('Не удалось прочитать загруженный файл.');
        }

        $xlsx = SimpleXLSX::parse($path);
        if ($xlsx === false) {
            throw new \InvalidArgumentException(SimpleXLSX::parseError());
        }

        $articles = [];
        foreach ($xlsx->rows() as $row) {
            $cell = isset($row[0]) ? trim((string) $row[0]) : '';
            if ($cell === '') {
                break;
            }
            $articles[] = $cell;
        }

        return array_values(array_unique($articles));
    }

    public function buildZipResponse(UploadedFile $file): BinaryFileResponse
    {
        $articles = $this->parseArticlesFromExcel($file);

        if ($articles === []) {
            abort(422, 'В файле не найдено ни одного артикула в столбце A (до первой пустой строки).');
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'catalog_photos_');
        if ($zipPath === false) {
            abort(500, 'Не удалось создать временный файл архива.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($zipPath);
            abort(500, 'Не удалось создать ZIP-архив.');
        }

        $entriesAdded = 0;

        foreach ($articles as $article) {
            $product = Product::where('vendor_code', $article)->first();
            if ($product === null) {
                continue;
            }

            $photos = ProductPhoto::where('uuid', $product->uuid)->orderBy('order')->get();
            if ($photos->isEmpty()) {
                continue;
            }

            $folder = $this->sanitizeZipPathSegment($article);
            $usedNames = [];

            foreach ($photos as $photo) {
                $originPath = str_replace('/micro/', '/origin/', $photo->file_path);
                $fullPath = storage_path('app/public') . str_replace('/storage/', '/', $originPath);

                if (!is_file($fullPath)) {
                    continue;
                }

                $entryName = $this->uniqueFileNameInFolder(
                    $photo->file_name . '.' . $photo->file_extension,
                    $usedNames
                );

                if ($zip->addFile($fullPath, $folder . '/' . $entryName)) {
                    $entriesAdded++;
                }
            }
        }

        $zip->close();

        if ($entriesAdded === 0) {
            @unlink($zipPath);
            abort(422, 'Не найдено ни одного файла фотографии для артикулов из списка.');
        }

        $downloadName = 'catalog_photos_' . date('Y-m-d_His') . '.zip';

        return response()->download($zipPath, $downloadName)->deleteFileAfterSend(true);
    }

    private function sanitizeZipPathSegment(string $name): string
    {
        $sanitized = preg_replace('/[\\\\\\/:*?"<>|]/u', '_', $name) ?? $name;

        return $sanitized !== '' ? $sanitized : 'article';
    }

    /**
     * @param array<string, true> $usedNames
     */
    private function uniqueFileNameInFolder(string $baseName, array &$usedNames): string
    {
        if (!isset($usedNames[$baseName])) {
            $usedNames[$baseName] = true;

            return $baseName;
        }

        $pathInfo = pathinfo($baseName);
        $filename = $pathInfo['filename'] ?? $baseName;
        $ext = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $i = 2;
        do {
            $candidate = $filename . '_' . $i . $ext;
            $i++;
        } while (isset($usedNames[$candidate]));

        $usedNames[$candidate] = true;

        return $candidate;
    }
}
