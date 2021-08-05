<?php

namespace App\Repositories;

use App\Enums\VoteOptionEnum;
use App\Exceptions\InvalidVoteOptionException;
use App\Exceptions\ScheduleHasSessionException;
use App\Exceptions\ScheduleNotHasSessionException;
use App\Exceptions\ScheduleSessionIsClosedException;
use App\Exceptions\UniqueVotePerSessionException;
use App\Models\Associate;
use App\Models\Schedule;
use App\Models\Vote;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class ScheduleRepository extends BaseRepository
{
    /** @var string */
    protected $modelClass = Schedule::class;

    /**
     * @param int $take
     * @param bool $paginate
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\AbstractPaginator|void
     */
    public function getAll(int $take = 15, bool $paginate = true)
    {
        return $this->doQuery($this->newQuery()->orderBy('created_at', 'DESC'), $take, $paginate);
    }

    /**
     * @param int $id
     * @param int|null $time
     *
     * @return Schedule
     * @throws ScheduleHasSessionException
     * @throws ScheduleSessionIsClosedException
     */
    public function openSession(int $id, int $time = null)
    {
        /** @var Schedule $schedule */
        $schedule = $this->findByID($id);

        if (!is_null($schedule->currentSession)) {
            throw new ScheduleHasSessionException();
        }

        if ($schedule->sessions->isNotEmpty()) {
            throw new ScheduleSessionIsClosedException();
        }

        $data = [];
        if (!is_null($time)) {
            $data['opening_time'] = $time;
        }

        $schedule->sessions()->create($data);
        $schedule->refresh();

        return $schedule;
    }

    /**
     * @param int $id
     *
     * @return Model
     * @throws Exception
     */
    public function closeSession(int $id)
    {
        /** @var Schedule $schedule */
        $schedule = $this->findByID($id);

        if (is_null($schedule->currentSession)) {
            throw new ScheduleNotHasSessionException();
        }
        $schedule->currentSession()->update([
            'closed_at' => Carbon::now(),
        ]);
        $schedule->refresh();

        return $schedule;
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return Schedule
     * @throws ScheduleSessionIsClosedException
     * @throws ScheduleNotHasSessionException
     * @throws UniqueVotePerSessionException
     */
    public function vote(int $id, array $data)
    {
        /** @var Associate $associate */
        $associate = $data['associate'];

        $option = $data['option'];

        /** @var Schedule $schedule */
        $schedule = $this->findByID($id);

        if (is_null($schedule->currentSession)) {

            if ($schedule->sessions->isNotEmpty()) {
                throw new ScheduleSessionIsClosedException();
            }

            throw new ScheduleNotHasSessionException();
        }

        /** @var Collection $votesFromThisAssociate */
        $votesFromThisAssociate = $schedule->currentSession->votes
            ->filter(function (Vote $vote) use ($associate) {
                return $vote->associate->id === $associate->id;
            });

        if ($votesFromThisAssociate->isNotEmpty()) {
            throw new UniqueVotePerSessionException();
        }

        $schedule->currentSession->votes()->create([
            'associate_id' => $associate->id,
            'option' => $option,
        ]);

        $schedule->refresh();

        return $schedule;
    }
}
