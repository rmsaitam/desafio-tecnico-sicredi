<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Illuminate\Http\JsonResponse;
use Exception;

class ScheduleHasSessionException extends Exception
{
    /**
     * @return JsonResponse
     */
    public function render()
    {
        return response()->json([
            'message' => trans('exceptions.This staff already has an open section'),
        ], HttpStatusCodeEnum::CONFLICT);
    }
}
