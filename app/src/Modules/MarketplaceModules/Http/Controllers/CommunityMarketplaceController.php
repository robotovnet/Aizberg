<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\MarketplaceModules\Entities\MarketplaceModuleQuestion;
use Modules\MarketplaceModules\Http\Requests\AnswerQuestionRequest;
use Modules\MarketplaceModules\Http\Requests\AskQuestionRequest;
use Modules\MarketplaceModules\Http\Requests\StoreRatingRequest;
use Modules\MarketplaceModules\Http\Requests\StoreReviewRequest;
use Modules\MarketplaceModules\Http\Requests\StoreReviewHelpfulnessRequest;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;

class CommunityMarketplaceController extends Controller
{
    public function __construct(
        private MarketplaceModuleRepository $repo
    ) {
    }

    public function askQuestion(AskQuestionRequest $request, string $slug): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findPublicModuleBySlug($slug);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден.']);
        }

        $q = $this->repo->askQuestion((int)$module->id, (int)$client->id, (string)$request->input('question_text'));

        return JsonResponse::successful([
            'id' => $q->id,
            'created_at' => $q->created_at?->toIso8601String(),
        ]);
    }

    public function answerQuestion(AnswerQuestionRequest $request, int $questionId): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $question = MarketplaceModuleQuestion::query()
            ->whereHas('module', function ($query) {
                $query->where('is_archived', false)->where('is_blocked', false);
            })
            ->find($questionId);
        if ($question === null) {
            return JsonResponse::failed(['message' => 'Вопрос не найден.']);
        }

        $a = $this->repo->answerQuestion($questionId, (int)$client->id, (string)$request->input('answer_text'));

        return JsonResponse::successful([
            'id' => $a->id,
            'created_at' => $a->created_at?->toIso8601String(),
        ]);
    }

    public function review(StoreReviewRequest $request, string $slug): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findPublicModuleBySlug($slug);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден.']);
        }

        $review = $this->repo->upsertReview((int)$module->id, (int)$client->id, (string)$request->input('review_text'));

        return JsonResponse::successful([
            'id' => $review->id,
            'updated_at' => $review->updated_at?->toIso8601String(),
        ]);
    }

    public function rate(StoreRatingRequest $request, string $slug): JsonResponse
    {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $module = $this->repo->findPublicModuleBySlug($slug);
        if ($module === null) {
            return JsonResponse::failed(['message' => 'Модуль не найден.']);
        }

        $rating = $this->repo->upsertRating(
            (int)$module->id,
            (int)$client->id,
            (int)$request->input('value')
        );

        $module->refresh();

        return JsonResponse::successful([
            'rating' => $rating->value,
            'module' => [
                'aggregate_rating' => $module->aggregate_rating,
                'ratings_count' => $module->ratings_count,
            ],
        ]);
    }

    public function markReviewHelpfulness(
        StoreReviewHelpfulnessRequest $request,
        string $slug,
        int $reviewId
    ): JsonResponse {
        /** @var Client $client */
        $client = Auth::guard('client')->user();

        $review = $this->repo->findPublicReview($slug, $reviewId);
        if ($review === null) {
            return JsonResponse::failed(['message' => 'Отзыв не найден.']);
        }

        $isHelpful = (bool)$request->boolean('is_helpful');
        $counts = $this->repo->upsertReviewVote($reviewId, (int)$client->id, $isHelpful);

        return JsonResponse::successful(array_merge([
            'review_id' => $reviewId,
            'is_helpful' => $isHelpful,
        ], $counts));
    }
}
