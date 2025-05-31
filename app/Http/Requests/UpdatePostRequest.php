<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ContentCheckCharatersLengthRule; // Make sure this rule exists and is correctly implemented

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // The PostPolicy will handle authorization for updating the post.
        // This FormRequest only checks if the user is authenticated.
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
            'title' => 'string|max:255',
            // The ContentCheckCharatersLengthRule needs access to 'platforms'
            // which should be available in the request after initial validation.
            'content' => ['string', 'max:63535', new ContentCheckCharatersLengthRule($this->platforms)],
            'image_file' => 'nullable|image|max:20480|mimes:png,jpg,webp,jpeg,svg', // Changed to image_file for clarity in form-data
            'status' => 'nullable|string|in:draft,scheduled,published',
            'scheduled_time' => 'nullable|date',
            'platforms' => 'array',
            'platforms.*' => 'exists:platforms,id',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // If you need to manipulate input before validation, do it here.
    }
}

