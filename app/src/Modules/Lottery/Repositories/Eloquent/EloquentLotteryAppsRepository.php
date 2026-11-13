<?php

declare(strict_types=1);

namespace Modules\Lottery\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Lottery\Entities\LotteryApps;
use Modules\Lottery\Repositories\LotteryAppsRepository;
use Modules\Email\Services\MailerService;
use Modules\Lottery\Mail\AcceptAppMail;
use Modules\Lottery\Mail\RejectAppMail;
use Illuminate\Support\Facades\Lang;
use Modules\Lottery\Entities\LotteryTickets;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
/**
 * Class EloquentLotteryAppsRepository
 * @package Modules\Lottery\Repositories\Eloquent
 */
class EloquentLotteryAppsRepository implements LotteryAppsRepository
{
    const STATUSES = [
        'accepted'  => 'Принято',
        'rejected'  => 'Отклонено',
        'winning'   => 'Выигрыш',
        'pending'   => 'На рассмотрении',
        'pending2'  => 'Повторное рассмотрение',
        'blocked'   => 'Заблокировано',
    ];

    public function __construct(
        private Model $model
    ) {}

    /**
     * @inheritDoc
     */
    public function setModel(int $id): self
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Lottery apps not found.');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(string $orderBy = 'id'): array
    {
        return LotteryApps::query()
            ->with('ticket')
            ->whereIn('status', ['pending', 'pending2'])
            ->orderBy($orderBy, 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'created_at' => Carbon::parse($item->created_at)->setTimezone('Europe/Moscow')->format('d.m.Y H:i'),
                    'ticket_id' => $item->ticket_id,
                    'player_name' => $item->player_name,
                    'player_phone' => $item->player_phone,
                    'player_email' => $item->player_email,
                    'status' => $item->status,
                    'screenshot_url' => $item->screenshot_url,
                    'ticket' => [
                        'code' => $item->ticket->code,
                        'status' => $item->ticket->status,
                    ]
                ];
            })
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function detail(int $id): array
    {
        $data = LotteryApps::query()
            ->with('ticket')
            ->findOrFail($id)
            ->toArray();

        $data['created_at'] = Carbon::parse($data['created_at'])->setTimezone('Europe/Moscow')->format('d.m.Y H:i');
        $data['status'] = self::STATUSES[$data['status']];

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function accept(int $id): void
    {
        $lotteryApp = LotteryApps::query()
            ->with('ticket')
            ->findOrFail($id);

        $lotteryApp->update(['status' => 'accepted']);
        $lotteryApp->ticket->update(['status' => 'registered']);

        $emailHtml = (new AcceptAppMail($lotteryApp->ticket->code))->render();

        $mailerService = new MailerService();
        $mailerService->send(Lang::get('lottery::emails/accept_app.subject'), $emailHtml, $lotteryApp->player_email);
    }

    /**
     * @inheritDoc
     */
    public function reject(int $id, ?string $rejectDesc = ''): void
    {
        if (is_null($rejectDesc)) {
            $rejectDesc = 'не указано';
        }

        $lotteryApp = LotteryApps::query()
            ->with('ticket')
            ->findOrFail($id);

        $statusTicket = 'unregistered';
        $statusApp = 'rejected';

        if ($lotteryApp->status === 'pending2') {
            $statusTicket = 'blocked';
            $statusApp = 'blocked';
        }

        $lotteryApp->update(['status' => $statusApp]);
        $lotteryApp->ticket->update(['status' => $statusTicket]);

        $emailHtml = (new RejectAppMail($lotteryApp->ticket->code, $rejectDesc))->render();

        $mailerService = new MailerService();
        $mailerService->send(Lang::get('lottery::emails/reject_app.subject'), $emailHtml, $lotteryApp->player_email);
    }

    /**
     * @inheritDoc
     */
    public function total(): int
    {
        return LotteryApps::query()
            ->whereIn('status', ['pending', 'pending2'])
            ->count();
    }

    /**
     * @inheritDoc
     */
    public function register(array $data): bool
    {
        // Ищем билет
        $ticket = LotteryTickets::query()
            ->where('code', $data['ticket_code'])
            ->first();

        if (!$ticket) {
            throw ValidationException::withMessages(
                ['ticket_not_found' => 'Билет не найден!']
            );
        }

        // Проверим, чтобы билет не был ранее зарегистрирован
        if (
            LotteryApps::query()
                ->where('status', '!=', 'rejected')
                ->where('ticket_id', $ticket->id)
                ->count() !== 0
        ) {
            throw ValidationException::withMessages(
                ['ticket_exist' => 'Данный билет ранее был уже зарегистрирован!']
            );
        }

        // Сохраняем скриншот
        $screenshotName = $this->saveImageToStorage($data['screenshot'], 'screenshot');
        
        if (
            LotteryApps::query()
                ->where('status', 'rejected')
                ->where('ticket_id', $ticket->id)
                ->count() > 0
        ) {
            // Обновляем данные заявки
            LotteryApps::query()
                ->where('ticket_id', $ticket->id)
                ->update(
                    [
                        'status' => 'pending2',
                        'player_name' => $data['player_name'],
                        'player_phone' => $data['player_phone'],
                        'player_email' => $data['player_email'],
                        'screenshot_url' => "/storage/lottery_screenshots/" . $screenshotName,
                    ]
                );
        } else {
            // Сохраняем заявку
            LotteryApps::query()
                ->create(
                    [
                        'ticket_id'      => $ticket->id,
                        'player_name'    => $data['player_name'],
                        'player_phone'   => $data['player_phone'],
                        'player_email'   => $data['player_email'],
                        'screenshot_url' => "/storage/lottery_screenshots/" . $screenshotName,
                        'status'         => 'pending',
                    ]
                );
        }

        // Помечаем билет зарегистрированным
        $ticket = LotteryTickets::find($ticket->id);
        $ticket->status = 'pending';
        $ticket->save();

        return true;
    }

    private function saveImageToStorage(UploadedFile $file, string $name): string
    {
        if (!@imagecreatefromstring(file_get_contents($file->getRealPath()))) {
            throw ValidationException::withMessages(
                ['invalid_image' => 'Вы загружаете некорректное изображение']
            );
        }

        $pathinfo  = pathinfo($file->getClientOriginalName());
        $fileExt   = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
        $fileName  = uniqid('lottery_screenshot_', true) . '.' . strtolower($fileExt);
        $toPath    = storage_path('app/public/lottery_screenshots/' . $fileName);

        move_uploaded_file($file->getRealPath(), $toPath);
        
        return $fileName;
    }
}
