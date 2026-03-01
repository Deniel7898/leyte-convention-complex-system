<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        // assume that only authenticated admins can hit these routes; adjust middleware separately
        return true;
    }

    public function rules()
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'string',
                'max:255',
                Rule::unique('users')->ignore($this->user ?? null),
            ],
            'phone' => ['nullable','string','max:25'],
            'is_admin' => ['sometimes','boolean'],
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } elseif ($this->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        return $rules;
    }
}
