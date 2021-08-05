<?php

namespace App\Http\Resources;

use App\Models\ScheduleSession;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ScheduleSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var ScheduleSession $session */
        $session = $this;
        return [
            'id' => $session->id,
            'time' => $session->opening_time,
            'opened_at' => $session->opened_at,
            'closed_at' => $session->closed_at,
        ];
    }
}
