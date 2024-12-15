<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Adjust this if authorization is needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:20',
            'status' => 'required|string|in:active,inactive,suspended',
            'timezone' => 'required|string',
            'language' => 'required|string',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'default_currency' => 'required|string|max:10',
            'avatar' => 'nullable|image|max:2048', // Ensure the image is within the max size (2MB)
            'role' => [
                'required',
                'string',
                Rule::in(['employee', 'Customer', 'Delivery']), // Add 'customer' to the allowed roles
            ],
            'password' => 'required|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*?&]/', // Password validation
        ];

        // Conditional validation based on the role
        if ($this->role === 'Customer') {
            $rules['preferred_payment_method'] = 'nullable|string|max:255';
            $rules['last_purchase_date'] = 'nullable|date';
            $rules['customer_preferences'] = 'nullable';
        } elseif ($this->role === 'employee') {
            $rules['hire_date'] = 'nullable|date';
            $rules['job_title'] = 'nullable|string|max:255';
            $rules['department'] = 'nullable|string|max:255';
            $rules['salary'] = 'nullable|numeric';
        }

        return $rules;
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'role.in' => 'The role must be either employee, or customer.',
            'avatar.image' => 'The avatar must be an image file.',
            'avatar.max' => 'The avatar must not be larger than 2MB.',
            'preferred_payment_method.string' => 'Preferred payment method must be a string.',
            'preferred_payment_method.max' => 'Preferred payment method must not exceed 255 characters.',
            'last_purchase_date.date' => 'Last purchase date must be a valid date.',
            'customer_preferences.json' => 'Customer preferences must be a valid JSON format.',
            'hire_date.date' => 'Hire date must be a valid date.',
            'job_title.string' => 'Job title must be a string.',
            'job_title.max' => 'Job title must not exceed 255 characters.',
            'department.string' => 'Department must be a string.',
            'department.max' => 'Department must not exceed 255 characters.',
            'salary.numeric' => 'Salary must be a number.',
        ];
    }
}
