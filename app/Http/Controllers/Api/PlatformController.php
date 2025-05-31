<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Platform;
use App\Models\PostPlatform;
use Illuminate\Http\Request;
use App\Models\UserActivePlatform;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::paginate(10);
        return response()->json($platforms);
    }




    public function toggleActive(Request $request, Platform $platform)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|exists:users,id'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $userId = $request->user_id ?? Auth::id();
        $userPlatform = UserActivePlatform::updateOrCreate(
            [
                'user_id' => $userId,
                'platform_id' => $platform->id,
            ],
            [
                'is_active' => !UserActivePlatform::where('user_id', $userId)
                    ->where('platform_id', $platform->id)
                    ->value('is_active') ?? true,
            ]
        );
        $updatedCount = Post::where('user_id', $userId)
                ->whereHas('platforms', function ($query) use ($platform) {
                    $query->where('platforms.id', $platform->id);
                })->get('id')->pluck('id');

        PostPlatform::whereIn('post_id', $updatedCount)->update(['platform_status' => $userPlatform->is_active]);

        return response()->json([
            'message' => 'Platform status updated successfully',
            'platform' => $platform,
            'is_active' => $userPlatform->is_active,
        ]);
    }

} 