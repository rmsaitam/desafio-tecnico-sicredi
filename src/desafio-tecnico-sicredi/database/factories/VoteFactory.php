<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Vote;
use Faker\Generator as Faker;

$factory->define(Vote::class, function (Faker $faker, $attribs = [
    'associate_id' => null,
    'schedule_session_id' => null,
]) {
    $associate_id = $attribs['associate_id'] ?? null;
    if (is_null($associate_id)) {
        $associate_id = factory(\App\Models\Associate::class)->create()->id;
    }
    $schedule_session_id = $attribs['schedule_session_id'] ?? null;
    if (is_null($schedule_session_id)) {
        $schedule_session_id = factory(\App\Models\ScheduleSession::class)->create()->id;
    }
    $optionValues = [ 'Y', 'N' ];

    return [
        'associate_id' => $associate_id,
        'schedule_session_id' => $schedule_session_id,
        'option' => $optionValues[rand(0,1)],
    ];
});
