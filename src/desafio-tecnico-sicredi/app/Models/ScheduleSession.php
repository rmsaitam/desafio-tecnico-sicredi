<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleSession extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'schedule_id',
        'opening_time',
        'opened_at',
        'closed_at'
    ];

    /** @var array  */
    protected $casts = [
        'opening_time' => 'integer'
    ];

    /**
     * @return BelongsTo
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * @return HasMany
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
