<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrowthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'recorded_date',
        'weight_kg',
        'height_cm',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'weight_kg' => 'decimal:2',
        'height_cm' => 'decimal:2',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Body Mass Index for this record (kg / m²).
     */
    public function getBmiAttribute(): ?float
    {
        if (! $this->height_cm || (float) $this->height_cm === 0.0) {
            return null;
        }

        $meters = (float) $this->height_cm / 100;

        return round((float) $this->weight_kg / ($meters * $meters), 1);
    }
}
