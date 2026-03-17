<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:280'],
            'body' => ['required', 'string'],
            'topic' => ['required', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'in:text,image,project_share,event_share,link_share,code_snippet'],
            'visibility' => ['nullable', 'string', 'in:public,followers'],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'link_label' => ['nullable', 'string', 'max:255'],
            'shareable_type' => ['nullable', 'string', 'in:project,event'],
            'shareable_id' => ['nullable', 'integer'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'max:8192'],
        ];
    }
}
