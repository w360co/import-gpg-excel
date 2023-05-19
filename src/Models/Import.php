<?php

namespace W360\ImportGpgExcel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Import extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo($this->attributes['author_type'] ?? \Illuminate\Foundation\Auth\User::class, 'author_id');
    }

}