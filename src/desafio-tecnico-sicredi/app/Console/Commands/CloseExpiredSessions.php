<?php

namespace App\Console\Commands;

use App\Models\ScheduleSession;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CloseExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (Schema::hasTable('schedule_sessions')) {
            $sessions = ScheduleSession::where('closed_at', null)->get();
            $sessions->map(function (ScheduleSession $session) {
                $openedAt = Carbon::createFromFormat('Y-m-d H:i:s', $session->opened_at);
                $closedAt = Carbon::createFromTimestamp($openedAt->getTimestamp() + $session->opening_time);
                if ($closedAt < Carbon::now()) {
                    $session->closed_at = $closedAt;
                    $session->save();
                }
            });
        }
    }
}
