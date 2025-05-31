<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostPlatform extends Model
{
    protected $fillable = [
        'post_id',
        'platform_id',
        'platform_status',

    ];
protected $table = 'post_platform';
  

    /**
     * Get the post that owns the platform connection.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the platform that is connected.
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * Scope a query to only include posts for a specific platform.
     */
    public function scopeForPlatform($query, $platformId)
    {
        return $query->where('platform_id', $platformId);
    }

    /**
     * Scope a query to only include posts with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include posts for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('post', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Get posts that are ready to be published.
     */
    public function scopeReadyToPublish($query)
    {
        return $query->where('status', 'scheduled')
            ->whereHas('post', function ($q) {
                $q->where('scheduled_time', '<=', now());
            });
    }

    /**
     * Mark the post as published on the platform.
     */
    public function markAsPublished($platformPostId = null, $metadata = [])
    {
        return $this->update([
            'status' => 'published',
            'published_at' => now(),
            'platform_post_id' => $platformPostId,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Mark the post as failed on the platform.
     */
    public function markAsFailed($errorMessage)
    {
        return $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Get platform-specific statistics.
     */
    public static function getPlatformStats($platformId, $userId = null)
    {
        $query = self::where('platform_id', $platformId);
        
        if ($userId) {
            $query->forUser($userId);
        }

        return $query->selectRaw('
            COUNT(*) as total_posts,
            SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) as published_count,
            SUM(CASE WHEN status = "scheduled" THEN 1 ELSE 0 END) as scheduled_count,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count,
            AVG(CASE WHEN status = "published" THEN 1 ELSE 0 END) * 100 as success_rate
        ')->first();
    }
} 