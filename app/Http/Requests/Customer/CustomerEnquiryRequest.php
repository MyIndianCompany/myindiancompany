<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerEnquiryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => ['required','numeric', 'regex:/^[0-9]{10}$/'],
            'email' => 'email',
            'service' => 'exists:services,id',
            'service_variant' => 'exists:service_variants,id',
            'message' => 'string|max:65535'
        ];
    }
}
