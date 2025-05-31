<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;

class PostRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') ) {
            $user = auth()->user();
            $todayPosts = Post::where('user_id', $user->id)
    
                ->whereDate('created_at', today())
                      ->whereColumn('scheduled_time', '>', 'created_at')
                ->count();
    

            if ($todayPosts >= 10) {
                return response()->json([
                    'message' => 'You have reached the daily limit of 10 scheduled posts.'
                ], 429);
            }
        }

        return $next($request);
    }
} 