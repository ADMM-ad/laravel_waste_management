<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tracking_code',
        'user_id',
        'category_id',
        'ai_predicted_category',
        'ai_confidence_score',
        'description',
        'photo_path',
        'latitude',
        'longitude',
        'address',
        'scale',
        'urgency',
        'status',
        'rejection_note',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'ai_confidence_score' => 'float',
    ];

    protected $appends = [
        'photo_url',
    ];

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->photo_path ? asset('storage/' . $this->photo_path) : null
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WasteCategory::class, 'category_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ReportStatusLog::class)->orderBy('created_at', 'desc');
    }
}
