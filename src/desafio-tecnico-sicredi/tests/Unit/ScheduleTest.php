<?php

namespace Tests\Unit;

use App\Enums\HttpStatusCodeEnum;
use App\Http\Resources\ScheduleResource;
use App\Models\Associate;
use App\Models\Schedule;
use App\Models\ScheduleSession;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    public function testCanCreateSchedule()
    {
        $data = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $this->post(route('schedules.store'), $data)
            ->assertStatus(HttpStatusCodeEnum::CREATED)
            ->assertJson($data);
    }

    public function testCanUpdateSchedule()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $data = [
            'title' => $this->faker->name,
            'description' => $this->faker->cpf,
        ];

        $this->put(route('schedules.update', $schedule->id), $data)
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($data);
    }

    public function testCanShowSchedule()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->get(route('schedules.show', $schedule->id))
            ->assertStatus(HttpStatusCodeEnum::SUCCESS);
    }

    public function testCanDeleteSchedule()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->delete(route('schedules.destroy', $schedule->id))
            ->assertStatus(HttpStatusCodeEnum::NO_CONTENT);
    }

    public function testCanListSchedules()
    {
        /** @var Collection $schedules */
        $schedules = factory(Schedule::class, 2)->create()->sortBy('created_at', null, true)
            ->map(function (Schedule $schedule) {
                return $schedule->only([ 'id', 'title', 'description' ]);
            });

        $this->get(route('schedules.index'))
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($schedules->toArray())
            ->assertJsonStructure([
                '*' =>[ 'id', 'title', 'description' ]
            ]);
    }

    public function testCanOpenScheduleSession()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $resource = new ScheduleResource($schedule);
        $expected = $resource->resolve();
        $expected['sessions'] = $resource->sessions->toArray();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), [])
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($expected);
    }

    public function testCanOpenScheduleSessionWithCustomTime()
    {
        $customTime = 600;

        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $resource = new ScheduleResource($schedule);
        $expected = $resource->resolve();
        $expected['sessions'] = $resource->sessions->toArray();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
            'time' => $customTime
        ]), [])
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($expected);

        $schedule->refresh();
        $this->assertEquals($customTime, $schedule->currentSession->opening_time);
    }

    public function testCanNotOpenScheduleSessionWhenAnotherIsOpened()
    {
        /** @var ScheduleSession $scheduleSession */
        $scheduleSession = factory(ScheduleSession::class)->create();

        $response = [
            'message' => trans('exceptions.This staff already has an open section'),
        ];

        $this->put(route('schedules.openSession', [
            'schedule' => $scheduleSession->schedule->id,
        ]), [])
            ->assertStatus(HttpStatusCodeEnum::CONFLICT)
            ->assertJson($response);
    }

    public function testCanNotOpenScheduleSessionWhenAnotherIsClosed()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $this->put(route('schedules.closeSession', [
            'schedule' => $schedule->id,
        ]), []);

        $response = [
            'message' => trans('exceptions.This schedule is already over'),
        ];

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), [])
            ->assertStatus(HttpStatusCodeEnum::FORBIDDEN)
            ->assertJson($response);
    }

    public function testCanCloseScheduleSession()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $schedule->refresh();

        $resource = new ScheduleResource($schedule);

        $response = $this->put(route('schedules.closeSession', [
            'schedule' => $schedule->id,
        ]), [])
            ->assertStatus(HttpStatusCodeEnum::SUCCESS);

        $expected = $resource->response()->getData(true);
        unset($expected['session_opened']);

        $this->assertSame($expected, $response->json());
    }

    public function testCanNotCloseScheduleSessionWhenNotHasOpened()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $response = [
            'message' => trans('exceptions.This staff does not have an open session'),
        ];

        $this->put(route('schedules.closeSession', [
            'schedule' => $schedule->id,
        ]), [])
            ->assertStatus(HttpStatusCodeEnum::NOT_FOUND)
            ->assertJson($response);
    }

    public function testCanVoteInScheduleSessionWithDataOfAssociateNotRegistered()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $schedule->refresh();

        $associateData = [
            'name' => $this->faker->name,
            'document' => $this->faker->cpf,
        ];

        $resource = new ScheduleResource($schedule);
        $expected = $resource->response()->getData(true);

        $this->put(route('schedules.vote', [
            'schedule' => $schedule->id,
        ]), [
            'option' => 'Y',
            'associate' => $associateData
        ])
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($expected);
    }

    public function testCanVoteInScheduleSessionWithDataOfAssociateRegistered()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $schedule->refresh();
        $associate = factory(Associate::class)->create();

        $resource = new ScheduleResource($schedule);
        $expected = $resource->response()->getData(true);

        $this->put(route('schedules.vote', [
            'schedule' => $schedule->id,
        ]), [
            'option' => 'Y',
            'associate_id' => $associate->id
        ])
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($expected);
    }

    public function testCanVoteInScheduleSessionWithDataOfAssociateRegisteredWithOnlyDocument()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $schedule->refresh();
        $associate = factory(Associate::class)->create();

        $resource = new ScheduleResource($schedule);
        $expected = $resource->response()->getData(true);

        $this->put(route('schedules.vote', [
            'schedule' => $schedule->id,
        ]), [
            'option' => 'Y',
            'associate' => [
                'document' => $associate->document
            ]
        ])
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($expected);
    }

    public function testCanNotVoteWithInvalidOption()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $schedule->refresh();
        $associate = factory(Associate::class)->create();

        $expected = [
            'message' => trans('exceptions.Validation error on uploaded data'),
        ];

        $this->put(route('schedules.vote', [
            'schedule' => $schedule->id,
        ]), [
            'option' => 'S',
            'associate_id' => $associate->id
        ])
            ->assertStatus(HttpStatusCodeEnum::BAD_REQUEST)
            ->assertJson($expected);
    }

    public function testCanNotVoteInScheduleSessionWhenAlreadyVote()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $schedule->refresh();
        $associate = factory(Associate::class)->create();

        $this->put(route('schedules.vote', [
            'schedule' => $schedule->id,
        ]), [
            'option' => 'Y',
            'associate_id' => $associate->id
        ]);

        $response = [
            'message' => trans('exceptions.You already voted for this session'),
        ];

        $this->put(route('schedules.vote', [
            'schedule' => $schedule->id,
        ]), [
            'option' => 'Y',
            'associate_id' => $associate->id
        ])
            ->assertStatus(HttpStatusCodeEnum::FORBIDDEN)
            ->assertJson($response);
    }

    public function testCanNotVoteInScheduleSessionWhenNotHasSessionOpened()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $associateData = [
            'name' => $this->faker->name,
            'document' => $this->faker->cpf,
        ];

        $response = [
            'message' => trans('exceptions.This staff does not have an open session'),
        ];

        $this->put(route('schedules.vote', [
            'schedule' => $schedule->id,
        ]), [
            'option' => 'Y',
            'associate' => $associateData
        ])
            ->assertStatus(HttpStatusCodeEnum::NOT_FOUND)
            ->assertJson($response);
    }

    public function testCanNotVoteInScheduleSessionWhenHasSessionClosed()
    {
        /** @var Schedule $schedule */
        $schedule = factory(Schedule::class)->create();

        $this->put(route('schedules.openSession', [
            'schedule' => $schedule->id,
        ]), []);

        $this->put(route('schedules.closeSession', [
            'schedule' => $schedule->id,
        ]), []);

        $associateData = [
            'name' => $this->faker->name,
            'document' => $this->faker->cpf,
        ];

        $response = [
            'message' => trans('exceptions.This schedule is already over'),
        ];

        $this->put(route('schedules.vote', [
            'schedule' => $schedule->id,
        ]), [
            'option' => 'Y',
            'associate' => $associateData
        ])
            ->assertStatus(HttpStatusCodeEnum::FORBIDDEN)
            ->assertJson($response);
    }
}
