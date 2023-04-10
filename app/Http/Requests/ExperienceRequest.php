<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExperienceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ];
    }
    
    public function messages()
    {
        return [
            'title.required' => 'Please enter a title for your experience.',
            'company.required' => 'Please enter the name of the company.',
            'start_date.required' => 'Please enter the start date of your experience.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        ];
    }
}
