<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ScheduleSession;
use Faker\Generator as Faker;

$factory->define(ScheduleSession::class, function (Faker $faker, $attribs = [
    'schedule_id' => null
]) {
    $schedule_id = $attribs['schedule_id'] ?? null;
    if (is_null($schedule_id)) {
        $schedule_id = factory(\App\Models\Schedule::class)->create()->id;
    }
    return [
        'schedule_id' => $schedule_id,
    ];
});
