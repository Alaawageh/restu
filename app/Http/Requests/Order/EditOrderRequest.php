<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'table_id' => [Rule::exists('tables' , 'id')],
            'branch_id' => [Rule::exists('branches' , 'id')],
            'takeaway' => 'in:true,false',
            'products' => 'array',
            'products.*.product_id' => [Rule::exists('products' , 'id')],
            'products.*.qty' => 'nullable|integer',
            'products.*.note' => 'nullable|string',
            'products.*.removedIngredients' => 'nullable|array',
            'products.*.extraIngredients' => 'nullable|array',
        ];
    }
}
