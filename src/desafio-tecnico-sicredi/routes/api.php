<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('v1')->namespace('v1')->group(function (){

    Route::resource('associates', 'AssociateController')->only([
        'index', 'show', 'store', 'update', 'destroy'
    ]);

    Route::resource('schedules', 'ScheduleController')->only([
        'index', 'show', 'store', 'update', 'destroy'
    ]);
    Route::put('schedules/{id}/openSession', 'ScheduleController@openSession')
        ->name('schedules.openSession');
    Route::put('schedules/{id}/closeSession', 'ScheduleController@closeSession')
        ->name('schedules.closeSession');
    Route::put('schedules/{id}/vote', 'ScheduleController@vote')
        ->name('schedules.vote');

    Route::resource('votes', 'VoteController')->only([
        'index'
    ]);
    Route::get('votes/result', 'VoteController@result')->name('votes.result');

});
