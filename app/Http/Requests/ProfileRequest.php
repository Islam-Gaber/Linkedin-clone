<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'headline' => 'required|max:255',
            'summary' => 'required',
            'country' => 'required',
            'state' => 'nullable',
            'city' => 'required',
            'address' => 'nullable',
            'phone_number' => 'nullable|numeric',
            'website_url' => 'nullable|url',
        ];
    }
}
