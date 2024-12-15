<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Return true to allow all users to make this request
        // Adjust authorization logic as needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'order_number' => [
                'required',
                Rule::unique('orders')->ignore($this->route('order'))
            ],
            'total_amount' => 'required|numeric',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_postal_code' => 'required|string',
            'shipping_country' => 'required|string',
            'shipping_phone' => 'required|string',
            'payment_status' => 'required|in:pending,paid,failed',
            'payment_method' => 'nullable|in:credit_card,paypal,bank_transfer,cash_on_delivery',
            'transaction_id' => 'nullable|string',
            'order_date' => 'required|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];

        // Add or modify rules based on whether the request is for updating an existing record
        if ($this->isMethod('post')) {
            // Post request - Creating a new order
            $rules['order_number'][] = 'unique:orders';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Put/Patch request - Updating an existing order
            // Assuming the route has an 'order' parameter with the order ID
            $orderId = $this->route('order');
            $rules['order_number'] = [
                'required',
                Rule::unique('orders')->ignore($orderId),
            ];
        }

        return $rules;
    }
}
