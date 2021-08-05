<?php

namespace App\Repositories;

use App\Enums\VoteOptionEnum;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use App\Models\Vote;

class VoteRepository extends BaseRepository
{
    /** @var string  */
    protected $modelClass = Vote::class;

    public function getResult(Schedule $schedule)
    {
        $allVotes = $schedule->sessions->map(function (ScheduleSession $session) {
            return $session->votes;
        })->flatten();

        $yesVotes = $allVotes->filter(function (Vote $vote) {
            return $vote->option === VoteOptionEnum::YES;
        });

        $noVotes = $allVotes->filter(function (Vote $vote) {
            return $vote->option === VoteOptionEnum::NO;
        });

        return [
            'is_open' => !is_null($schedule->currentSession),
            'total' => $allVotes->count(),
            VoteOptionEnum::YES => $yesVotes->count(),
            VoteOptionEnum::NO => $noVotes->count(),
        ];
    }

    /**
     * @param Schedule $schedule
     *
     * @return mixed
     */
    public function getAllVotes(Schedule $schedule)
    {
        return $schedule->sessions->map(function (ScheduleSession $session) {
            return $session->votes;
        })->flatten();

    }

}
