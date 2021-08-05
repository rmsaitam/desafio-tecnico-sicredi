<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use SoftDeletes;

    /** @var array */
    protected $fillable = [
        'title',
        'description'
    ];

    /**
     * @return HasMany
     */
    public function sessions()
    {
        return $this->hasMany(ScheduleSession::class);
    }

    /**
     * @return BelongsTo
     */
    public function currentSession()
    {
        return $this->belongsTo(ScheduleSession::class, 'session_opened');
    }
}
