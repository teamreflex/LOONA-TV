<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $fillable = [
        'title',
        'videoId',
        'arc_id',
    ];

    /**
     * @return BelongsTo
     */
    public function arc(): BelongsTo
    {
        return $this->belongsTo(Arc::class);
    }
}
