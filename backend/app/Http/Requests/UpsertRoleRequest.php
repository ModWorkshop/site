<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertRoleRequest extends FormRequest
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
            'name' => 'string|nullable|min_strict:2|max:30',
            'tag' => 'string|nullable|min_strict:2|max:30',
            'desc' => 'string|nullable|max:1000',
            'color' => 'string|nullable|max:8',
            'is_vanity' => 'boolean',
            'self_assignable' => 'boolean',
            'order' => 'integer|nullable|max:1001',
            'permissions' => 'array|nullable',
        ];
    }
}
