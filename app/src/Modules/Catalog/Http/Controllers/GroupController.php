<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Catalog\Entities\Feature;
use Modules\Catalog\Http\Requests\CreateOrUpdateGroupRequest;
use Modules\Catalog\Repositories\GroupRepository;
use Illuminate\Http\Request;

/**
 * Class GroupController
 * @package Modules\Catalog\Http\Controllers
 */
class GroupController extends Controller
{
    /** @var GroupRepository An instance of the Group Repository. */
    private GroupRepository $groupRepo;

    /**
     * GroupController constructor.
     */
    public function __construct(GroupRepository $groupRepo)
    {
        $this->groupRepo = $groupRepo;
    }

    /**
     * Returns all groups.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search', null);

        return JsonResponse::successful($this->groupRepo->all($search));
    }

    /**
     * Creates a new group.
     */
    public function store(CreateOrUpdateGroupRequest $request): JsonResponse
    {
        $request->merge([
            'plural_name'     => Str::getNamesJson($request->plural_names),
            'singular_name'   => Str::getNamesJson($request->singular_names),
            'overview_text'   => Str::getNamesJson($request->overview_texts),
        ]);
        
        // Обработка поля where_sn_desc если оно передано
        if ($request->has('where_sn_descs')) {
            $request->merge([
                'where_sn_desc' => Str::getNamesJson($request->where_sn_descs),
            ]);
        }

        $groupId = $this->groupRepo->create($request->all());

        return JsonResponse::successful([
            'id' => $groupId,
            'message' => 'success',
        ]);
    }

    /**
     * Returns group data by id.
     */
    public function show(GroupRepository $groupRepo): JsonResponse
    {
        return JsonResponse::successful($groupRepo->data());
    }

    /**
     * Updates group data.
     */
    public function update(
        CreateOrUpdateGroupRequest $request,
        GroupRepository $groupRepo
    ): JsonResponse {
        $request->merge([
            'plural_name'     => Str::getNamesJson($request->plural_names),
            'singular_name'   => Str::getNamesJson($request->singular_names),
            'overview_text'   => Str::getNamesJson($request->overview_texts),
        ]);
        
        // Обработка поля where_sn_desc если оно передано
        if ($request->has('where_sn_descs')) {
            $request->merge([
                'where_sn_desc' => Str::getNamesJson($request->where_sn_descs),
            ]);
        }

        $groupRepo->update($request->all());

        return JsonResponse::successful();
    }

    /**
     * Deletes the group.
     */
    public function destroy(GroupRepository $groupRepo): JsonResponse
    {
        $groupRepo->delete();

        return JsonResponse::successful();
    }

    /**
     * Relationship: List of features product group.
     */
    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }
}
