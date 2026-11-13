<?php

declare(strict_types=1);

namespace Modules\ImageUploader\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Modules\ImageUploader\Entities\ImageUpload;
use Modules\ImageUploader\Http\Requests\CreateOrUpdateImageUploadRequest;
use Modules\ImageUploader\Services\ImageUploaderService;

class ImageUploaderController extends Controller
{
    public function __construct(private readonly ImageUploaderService $service)
    {
    }

    public function upload(CreateOrUpdateImageUploadRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $category = $validated['category'] ?? null;
        $title = trim((string) ($validated['title'] ?? $request->input('title', '')));

        /** @var list<UploadedFile> $uploaded */
        $uploaded = [];

        if ($request->file('image') instanceof UploadedFile) {
            $uploaded = [$request->file('image')];
        } else {
            $uploaded = $this->normalizedImagesFromRequest($request);
        }

        if (count($uploaded) === 1) {
            $image = $this->service->upload($uploaded[0], $title, $category);

            return response()->json([
                'data' => $this->formatImage($image),
            ], 201);
        }

        $data = [];
        foreach ($uploaded as $file) {
            $data[] = $this->formatImage(
                $this->service->upload($file, $this->titleFromUploadedFile($file), $category)
            );
        }

        return response()->json([
            'data' => $data,
        ], 201);
    }

    public function all(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query('page', 1));
        $category = $request->query('category');

        $paginator = $this->service->list($category, $page);
        $collection = collect($paginator->items())->map(fn (ImageUpload $image) => $this->formatImage($image))->values();

        return response()->json([
            'data' => $collection,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function data(ImageUpload $image): JsonResponse
    {
        return response()->json([
            'data' => $this->formatImage($image, includeTimestamps: true),
        ]);
    }

    public function update(CreateOrUpdateImageUploadRequest $request, ImageUpload $image): JsonResponse
    {
        $validated = $request->validated();

        $image = $this->service->updateInfo(
            $image,
            $validated['title'],
            $validated['category'] ?? null
        );

        return response()->json([
            'data' => $this->formatImage($image, includeTimestamps: true),
        ]);
    }

    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => $this->service->categories(),
        ]);
    }

    public function delete(ImageUpload $image): JsonResponse
    {
        $this->service->delete($image);

        return response()->json([
            'status' => 'ok',
        ]);
    }

    /**
     * @return list<UploadedFile>
     */
    private function normalizedImagesFromRequest(Request $request): array
    {
        $raw = $request->file('images');
        if ($raw === null) {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(
                $raw,
                static fn ($file): bool => $file instanceof UploadedFile
            ));
        }

        return $raw instanceof UploadedFile ? [$raw] : [];
    }

    private function titleFromUploadedFile(UploadedFile $file): string
    {
        $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        return $base !== '' && $base !== false ? $base : 'image';
    }

    private function formatImage(ImageUpload $image, bool $includeTimestamps = false): array
    {
        $urls = $this->service->urls($image);

        $data = [
            'id' => $image->id,
            'title' => $image->title,
            'category' => $image->category,
            'original_url' => $urls['original'],
            'micro_url' => $urls['micro'],
        ];

        if ($includeTimestamps) {
            $data['created_at'] = optional($image->created_at)->toDateTimeString();
            $data['updated_at'] = optional($image->updated_at)->toDateTimeString();
        }

        return $data;
    }
}

