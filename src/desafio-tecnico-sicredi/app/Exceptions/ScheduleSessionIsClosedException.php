<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Exception;

class ScheduleSessionIsClosedException extends Exception
{
    public function render()
    {
        return response()->json([
            'message' => trans('exceptions.This schedule is already over'),
        ], HttpStatusCodeEnum::FORBIDDEN);
    }
}
