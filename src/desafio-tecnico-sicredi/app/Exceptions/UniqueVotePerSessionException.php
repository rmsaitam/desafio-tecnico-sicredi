<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Exception;

class UniqueVotePerSessionException extends Exception
{
    public function render()
    {
        return response()->json([
            'message' => trans('exceptions.You already voted for this session'),
        ], HttpStatusCodeEnum::FORBIDDEN);
    }
}
