<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetThreadRequest extends FilteredRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'category_name' => 'string|max:100|nullable',
            'category_id' => 'integer|min:1|nullable|exists:forum_categories,id',
            'tags' => 'array|max:10',
            'tags.*' => 'integer|min:1|nullable',
            'no_pins' => 'boolean|nullable',
            'closed' => 'boolean|nullable',
            'forum_id' => 'integer|min:1|nullable|exists:forums,id',
        ];
    }
}
