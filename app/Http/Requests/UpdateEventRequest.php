<?php

namespace App\Http\Requests;

class UpdateEventRequest extends StoreEventRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        foreach (['title', 'summary', 'details', 'format', 'starts_at', 'ends_at'] as $field) {
            $rules[$field][0] = 'sometimes';
        }

        return $rules;
    }
}
