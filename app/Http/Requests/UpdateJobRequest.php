<?php

namespace App\Http\Requests;

class UpdateJobRequest extends StoreJobRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        foreach (['company_name', 'title', 'summary', 'description', 'job_type', 'work_mode', 'experience_level'] as $field) {
            $rules[$field][0] = 'sometimes';
        }

        return $rules;
    }
}
