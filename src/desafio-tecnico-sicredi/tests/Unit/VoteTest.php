<?php

namespace Tests\Unit;

use App\Enums\HttpStatusCodeEnum;
use App\Enums\VoteOptionEnum;
use App\Models\Associate;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use App\Models\Vote;
use Illuminate\Support\Collection;
use Tests\TestCase;

class VoteTest extends TestCase
{
    public function testCanListVotes()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        /** @var Schedule $schedule */
        $scheduleSession = factory(ScheduleSession::class)->create([
            'schedule_id' => $schedule->id,
        ]);

        /** @var Collection $votes */
        $votes = factory(Vote::class, 2)->create([
            'schedule_session_id' => $scheduleSession->id,
        ]);

        $jsonExpected = $votes->map(function (Vote $vote) {
            return $vote->only([ 'option' ]);
        })->toArray();

        $this->get(route('votes.index', [
            'schedule_id' => $schedule->id,
        ]))
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($jsonExpected)
            ->assertJsonStructure([
                '*' => [ 'option' ]
            ]);
    }

    public function testCanGetResultEmptyOfScheduleWhenNotSessionOpened()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $response = [
            'is_open' => false,
            'total' => 0,
            VoteOptionEnum::YES => 0,
            VoteOptionEnum::NO => 0,
        ];

        $this->get(route('votes.result', [
            'schedule_id' => $schedule->id
        ]))
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($response);
    }

    public function testCanGetResultOfScheduleWhenVotesAreSubmitted()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $totalVotes = 5;
        $yesVotes = 0;
        $noVotes = 0;
        $options = [VoteOptionEnum::YES, VoteOptionEnum::NO];

        for ($i = 0; $i < $totalVotes; $i++) {
            $associate = factory(Associate::class)->create();
            $option = rand(0, 1);

            switch($options[$option]) {
                case VoteOptionEnum::YES:
                    $yesVotes++;
                    break;
                case VoteOptionEnum::NO:
                    $noVotes++;
                    break;
            }

            $this->put(route('schedules.vote', [
                'schedule' => $schedule->id,
            ]), [
                'option' => $options[$option],
                'associate_id' => $associate->id
            ]);
        }

        $this->put(route('schedules.closeSession', [
            'schedule' => $schedule->id,
        ]), []);

        $response = [
            'is_open' => false,
            'total' => $totalVotes,
            VoteOptionEnum::YES => $yesVotes,
            VoteOptionEnum::NO => $noVotes,
        ];

        $this->get(route('votes.result', [
            'schedule_id' => $schedule->id
        ]))
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($response);
    }
}
