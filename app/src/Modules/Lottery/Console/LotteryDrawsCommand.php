<?php

namespace Modules\Lottery\Console;

use Illuminate\Console\Command;
use Modules\Lottery\Entities\LotteryTickets;
use Modules\Lottery\Entities\LotteryPrizes;
use Modules\Lottery\Entities\LotteryDraws;
use Modules\Lottery\Entities\LotteryApps;

class LotteryDrawsCommand extends Command
{
    protected $name = 'lottery:draws';
    protected $description = 'Draws lottery tickets.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): bool
    {
        $this->comment("Start");

        $ticket = LotteryTickets::query()
            ->where('status', 'registered')
            ->inRandomOrder()
            ->first();

        if (!$ticket) {
            $this->comment("Нет доступных лотерейных билетов\n");
            return false;
        }

        $prize = LotteryPrizes::where('status', 'playing')->inRandomOrder()->first();

        if (!$prize) {
            $this->comment("Нет доступных призов\n");
            return false;
        }

        LotteryPrizes::where('id', $prize->id)->update(['status' => 'raffled_off']);
        LotteryTickets::where('id', $ticket->id)->update(['status' => 'winning']);

        $winApplication = LotteryApps::where('ticket_id', $ticket->id)->first();
        LotteryApps::where('ticket_id', $ticket->id)->update(['status' => 'winning']);

        $lotteryDraw = new LotteryDraws();
        $lotteryDraw->day       = date('d.m');
        $lotteryDraw->week      = date('W');
        $lotteryDraw->year      = date('Y');
        $lotteryDraw->ticket_id = $ticket->id;
        $lotteryDraw->prize_id  = $prize->id;
        $lotteryDraw->app_id    = $winApplication->id;
        $lotteryDraw->save();

        $this->comment("Билет {$ticket->code} выиграл приз {$prize->name}\n");
        $this->comment("End");

        return true;
    }
}
