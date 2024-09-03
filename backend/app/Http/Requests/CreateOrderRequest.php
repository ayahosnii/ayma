<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'user_id' => 'required|exists:users,id',
            'order_number' => 'required|unique:orders',
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
        ];
    }
}
