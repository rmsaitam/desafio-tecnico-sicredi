<?php

namespace App\Http\Resources;

use App\Models\Schedule;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Schedule $schedule */
        $schedule = $this;
        return [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'session_opened' => $this->when(
                !is_null($schedule->currentSession),
                new ScheduleSessionResource($schedule->currentSession)
            ),
            'sessions' => ScheduleSessionResource::collection($this->sessions),
        ];
    }
}
