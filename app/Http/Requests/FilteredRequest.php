<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Essentially used to filter results and control pagination of stuff to avoid copypasting
 * 
 */
class FilteredRequest extends FormRequest
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
            'query' => 'string|nullable',
            'limit' => 'integer|min:1|max:50'
        ];
    }

    public function val(array $rules=[], ...$params)
    {
        return [...$this->validated(), ...$this->validate($rules, ...$params)];
    }
}
