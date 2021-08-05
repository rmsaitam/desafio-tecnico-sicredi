<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use SoftDeletes;

    /** @var array */
    protected $fillable = [
        'option',
        'associate_id'
    ];

    /**
     * @return BelongsTo
     */
    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }
}
