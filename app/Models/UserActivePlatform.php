<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivePlatform extends Model
{
    protected $fillable = [
        'user_id',
        'platform_id',
        'is_active',

    ];

    protected $casts = [
        'is_active' => 'boolean',
       
    ];

    /**
     * Get the user that owns the platform connection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the platform that is connected.
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
} 