<?php

namespace App\Http\Requests;

class UpdatePostRequest extends StorePostRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['title'][0] = 'sometimes';
        $rules['body'][0] = 'sometimes';
        $rules['topic'][0] = 'sometimes';

        return $rules;
    }
}
