<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
function create($action,$message,$user=null){
    ActivityLog::create([
        'user_id' => $user || request()->user()?->id || auth()->id(),
        'action' => $action,
        'description' => $message,
    ]);
}
} 