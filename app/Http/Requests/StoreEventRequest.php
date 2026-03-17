<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'format' => ['required', 'string', 'in:Online,Onsite,Hybrid'],
            'status' => ['nullable', 'string', 'in:draft,upcoming,ongoing,ended,cancelled'],
            'workshop_type' => ['nullable', 'string', 'max:100'],
            'venue' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'registration_url' => ['nullable', 'url', 'max:2048'],
            'organizer_name' => ['nullable', 'string', 'max:255'],
            'organizer_url' => ['nullable', 'url', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'publish' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'image', 'max:8192'],
        ];
    }
}
