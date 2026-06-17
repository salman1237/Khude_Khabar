<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildHealthCondition extends Model
{
    protected $fillable = ['child_id', 'condition_name'];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}
