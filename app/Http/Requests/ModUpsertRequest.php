<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModUpsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'string|min:3|max:150',
            'desc' => 'string|min:3|max:30000',
            'license' => 'string|nullable|max:30000',
            'changelog' => 'string|nullable|max:30000',
            'short_desc' => 'string|nullable|max:150',
            'donation' => 'string|nullable|max:100',
            'version' => 'string|nullable|max:100',
            'visibility' => 'integer|min:1|max:4',
            'game_id' => 'integer|nullable|min:1|exists:games,id',
            'category_id' => 'integer|min:1|nullable|exists:categories,id',
            'thumbnail_id' => 'integer|min:1|nullable|exists:images,id',
            'tag_ids' => 'array',
            'tag_ids.*' => 'integer|min:1',
            'download_id' => 'integer|min:1|nullable',
            'download_type' => 'string|nullable|in:file,link'
        ];
    }
}
