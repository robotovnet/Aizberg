<?php

declare(strict_types=1);

namespace Modules\Qa\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Modules\Qa\Entities\Qa;
use Modules\Qa\Repositories\QaRepository;
use Modules\Settings\Entities\Settings;
use Modules\Catalog\Entities\Product;
use Modules\Client\Entities\Client;
use Modules\Staff\Entities\Staff;
use Modules\Email\Services\MailerService;
use Carbon\Carbon;
use Modules\Qa\Mail\AnswerToQuestion;

/**
 * Class EloquentQaRepository
 * @package Modules\Qa\Repositories\Eloquent
 */
class EloquentQaRepository implements QaRepository
{
    /** @var Model An instance of the Eloquent Model. */
    public Model $model;

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
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Qa not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): int
    {
        $this->model = $this->model->create($data);

        return $this->model->id;
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes): self
    {
        $this->model->update($attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): self
    {
        $this->model->delete();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(?string $isReplied, ?string $onlyCount, int $page = 1): array
    {
        $query = Qa::whereNull('reply_to_qid')
            ->when(!is_null($isReplied), function ($query) use ($isReplied) {
                return $query->where('replied', $isReplied);
            });

        if (is_null($onlyCount)) {
            $allQa = $query
                ->skip(($page - 1) * (int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'])
                ->take((int)Settings::where('code', 'ENTITY_PER_PAGE')->first()->toArray()['value'])
                ->orderBy('id', 'desc')
                ->get(Qa::SELECT_ATTRIBUTES)
                ->toArray();

            foreach ($allQa as $key => $qa) {
                $answers = Qa::where('reply_to_qid', $qa['id'])->orderBy('id', 'desc')->get();

                $allQa[$key]['answer_text'] = null;
                $allQa[$key]['staff_name'] = null;

                if ($answers) {
                    $answersArr = [];
                    $staffArr = [];

                    foreach ($answers as $answer) {
                        $answersArr[] = $answer->content;

                        $staff = Staff::find($answer->staff_id);
                        $staffArr[] = $staff->first_name . " " . $staff->last_name;
                    }

                    $allQa[$key]['answer_text'] = implode(" ", $answersArr);
                    $allQa[$key]['staff_name']  = implode(", ", array_unique($staffArr));
                }

                $product = Product::find($allQa[$key]['product_id']);
                if ($product) {
                    $productName = json_decode($product->name)->ru;
                    $allQa[$key]['product_url']  = \Helper::productUrl($productName, (int) $product->id);
                    $allQa[$key]['product_url'] = str_replace("api.", "", $allQa[$key]['product_url']);
                    $allQa[$key]['product_name'] = $productName;
                } else {
                    $allQa[$key]['product_url'] = "";
                    $allQa[$key]['product_name'] = "unknown product";
                }

                $client = Client::find($allQa[$key]['client_id']);
                if ($client) {
                    $allQa[$key]['client_phone'] = $client->phone;
                    $allQa[$key]['client_email'] = $client->email;
                    $allQa[$key]['client_name']  = $client->first_name . " " . $client->last_name;
                } else {
                    $allQa[$key]['client_phone'] = "";
                    $allQa[$key]['client_email'] = "";
                    $allQa[$key]['client_name']  = "unknown client";
                }

                $allQa[$key]['created_at'] = Carbon::parse($allQa[$key]['created_at'])->setTimezone('Europe/Moscow')->format('d.m.Y H:i');
            }

            return $allQa;
        } else {
            return ['total' => $query->count()];
        }
    }

    /**
     * @inheritDoc
     */
    public function answer(Model $questionModel, string $answerText): bool
    {
        $questionModel->replied = 'Y';
        $questionModel->save();

        $data['product_id']    = $questionModel->product_id;
        $data['client_id']     = null;
        $data['staff_id']      = Auth::id();
        $data['reply_to_qid']  = $questionModel->id;
        $data['replied']       = 'Y';
        $data['content']       = strip_tags($answerText);
        $data['reply_to_mail'] = 'N';

        $this->model->create($data);

        // Отправить сообщение об ответе на вопрос
        if ($questionModel->reply_to_mail == 'Y') {
            $client = Client::find($questionModel->client_id);
            $product = Product::find($questionModel->product_id);

            if ($client && $product) {
                $productName = json_decode($product->name)->ru;

                $productUrl  = \Helper::productUrl($productName, (int) $product->id);
                $productUrl = str_replace("api.", "", $productUrl);

                $html = (new AnswerToQuestion($productUrl, $productName, $questionModel->content, strip_tags($answerText)))->render();

                $mailerService = new MailerService();
                $mailerService->send(Lang::get('qa::emails/answer_to_question.subject'), $html, $client->email);
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        return Qa::find($this->model->id, Qa::SELECT_ATTRIBUTES)->toArray();
    }
}
