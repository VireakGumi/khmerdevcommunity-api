<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_logo_url' => ['nullable', 'url', 'max:2048'],
            'company_website' => ['nullable', 'url', 'max:2048'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'job_type' => ['required', 'string', 'in:full_time,part_time,freelance,internship'],
            'work_mode' => ['required', 'string', 'in:remote,onsite,hybrid'],
            'experience_level' => ['required', 'string', 'in:intern,junior,mid,senior,lead'],
            'location' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'max:10'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.*' => ['string', 'max:50'],
            'apply_url' => ['nullable', 'url', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,active,closed'],
            'publish' => ['nullable', 'boolean'],
        ];
    }
}
