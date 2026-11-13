<?php

declare(strict_types=1);

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\Client\Http\Requests\UploadClientUserpicRequest;
use Modules\Client\Services\ClientUserpicService;

class ClientUserpicController extends Controller
{
    public function __construct(private ClientUserpicService $userpicService)
    {
    }

    public function store(UploadClientUserpicRequest $request): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();
        $userpic = $this->userpicService->replace($client, $request->file('userpic'));

        return JsonResponse::successful([
            'id' => $userpic->id,
            'url' => $this->userpicService->publicUrl($userpic->path),
            'mime_type' => $userpic->mime_type,
            'size' => $userpic->size,
        ]);
    }
}
