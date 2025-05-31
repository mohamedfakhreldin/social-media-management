<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'char_limit',
        
    ];
    public function userActivePlatforms(): HasMany
    {
        return $this->hasMany(UserActivePlatform::class);
    }
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class,'post_platform')
            ->withPivot('platform_status')
            ->withTimestamps();
    }
} 