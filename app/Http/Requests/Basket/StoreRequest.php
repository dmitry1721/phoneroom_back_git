<?php

namespace App\Http\Requests\Basket;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'integer|required|injection',
            'user_id' => 'integer|required|injection',
            'quantity' => 'integer|nullable|injection',
        ];
    }
}
