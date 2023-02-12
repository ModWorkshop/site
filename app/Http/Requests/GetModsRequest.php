<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetModsRequest extends FilteredRequest
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
            ...parent::rules(),
            // How many mods should this return. 
            'game_id' => 'integer|nullable|min:1|exists:games,id',
            'category_id' => 'integer|nullable|min:1|exists:categories,id',
            'tags' => 'array|max:10',
            'liked' => 'boolean',
            'tags.*' => 'integer|min:1|nullable',
            'categories' => 'array|max:10',
            'categories.*' => 'integer|min:1|nullable',
            'block_tags' => 'array|max:10',
            'block_tags.*' => 'integer|min:1|nullable',
            'user_id' => 'integer|nullable|min:1',
            'collab' => 'boolean',
            'sort' => Rule::in([
                'bumped_at',
                'published_at',
                'likes',
                'downloads',
                'views',
                'score',
                'weekly_score',
                'daily_score',
                'random',
                'name'
            ])
        ];
    }
}
