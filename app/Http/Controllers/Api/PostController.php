<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use App\Traits\HandlesImageStorage;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use App\Http\Requests\StorePostRequest; // Import the new StorePostRequest
use App\Http\Requests\UpdatePostRequest; // Import the new UpdatePostRequest
use App\Rules\ContentCheckCharatersLengthRule; // Ensure this rule is defined

class PostController extends Controller
{
    
    use AuthorizesRequests,LogsActivity, HandlesImageStorage;

    /**
     * Display a listing of the user's posts.
     */
    public function index(Request $request)
    {
        $query = $request->user()->posts();

        $query->when($request->has('status'), function ($query) use ($request) {
            $query->where('status', $request->status);
        });

        $query->when($request->has('date'), function ($query) use ($request) {
            $query->whereDate('scheduled_time', $request->date);
        });

        return response()->json($query->with('platforms')->paginate(10));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(StorePostRequest $request)
    {
        // Validation is handled by StorePostRequest
        $validated = $request->validated();
        $imagePath = $this->storeImage($request->file('image_file'));
        $validated['image_url'] = $imagePath;
if(isset($validated['status']) && $validated['status']!=='scheduled'){
    $validated['scheduled_time'] = now();
}
        $post = $request->user()->posts()->create($validated);

        $post->platforms()->attach($validated['platforms']);

        return response()->json($post->load('platforms'), 201);
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post); // Authorize viewing the post
        return response()->json($post->load('platforms'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post); // Authorize updating the post

        if ($post->status === 'scheduled') {
            // Validation is handled by UpdatePostRequest
            $validated = $request->validated();
            if($validated['status']=='scheduled'){
    $validated['scheduled_time'] = now();
}
            $imagePath = $post->image_url; // Keep existing image path by default
            if ($request->hasFile('image_file')) {
                $this->deleteImage($post->image_url);
                $imagePath = $this->storeImage($request->file('image_file'));
            }
        

            $post->update($validated+ ['image_url' => $imagePath]);

            if ($request->has('platforms')) {
                $post->platforms()->sync($validated['platforms']);
            }

            return response()->json($post->load('platforms'));
        }

        // If the post is not 'scheduled', return an error or specific response
        return response()->json(['message' => 'Cannot update a post that is not in "scheduled" status.'], 403);
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post); // Authorize deleting the post

        // Delete associated image file if it exists
        $this->deleteImage($post->image_url);


        $post->delete();
        return response()->json(null, 204);
    }

    /**
     * Filter posts based on criteria.
     * This method was already present and remains largely the same.
     * It's good practice to have a dedicated filter method if filtering logic becomes complex.
     */
    public function filter(Request $request)
    {
        // This method was already present and remains largely the same.
        // You might consider moving the filtering logic into a dedicated
        // query scope on the Post model for larger applications.
        $query = $request->user()->posts();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('scheduled_time', $request->date);
        }

        return response()->json($query->with('platforms')->get());
    }
}
