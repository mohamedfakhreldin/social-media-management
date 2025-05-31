<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ContentCheckCharatersLengthRule; // Make sure this rule exists and is correctly implemented

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed.
        // For now, assuming authenticated users can create posts.
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            'title' => 'required|string|max:255',
            'platforms' => 'required|array',
            // The ContentCheckCharatersLengthRule needs access to 'platforms'
            // which should be available in the request after initial validation.
            'content' => ['string', 'max:63535', new ContentCheckCharatersLengthRule($this->platforms)],
            'image_file' => 'nullable|image|max:20480|mimes:png,jpg,webp,jpeg,svg', // Changed to image_file for clarity in form-data
            'status' => 'nullable|string|in:draft,scheduled,published',
            'scheduled_time' => 'nullable|date',
            'platforms.*' =>[  \Illuminate\Validation\Rule::exists('user_active_platforms', 'platform_id')
        ->where(function ($query) {
            $query->where('user_id', $this->user()->id);
        }),],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
   
}

