<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Exception;

class ScheduleNotHasSessionException extends Exception
{
    public function render()
    {
        return response()->json([
            'message' => trans('exceptions.This staff does not have an open session'),
        ], HttpStatusCodeEnum::NOT_FOUND);
    }
}
