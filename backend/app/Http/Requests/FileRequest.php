<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|min_strict:1|max:100',
            'label' => 'string|nullable|max:100',
            'desc' => 'string|nullable|max:1000',
            'version' => 'string|nullable|max:255',
            'display_order' => 'integer|min:-1000|max:1000|nullable',
            'image_id' => 'int|nullable|exists:images,id'
        ];
    }

    public function val(array $rules=[], ...$params)
    {
        return [...$this->validated(), ...$this->validate($rules, ...$params)];
    }
}
