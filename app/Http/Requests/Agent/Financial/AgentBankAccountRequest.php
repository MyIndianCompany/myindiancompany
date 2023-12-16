<?php

namespace App\Http\Requests\Agent\Financial;

use Illuminate\Foundation\Http\FormRequest;

class AgentBankAccountRequest extends FormRequest
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
            'agent_id'            => 'exists:users,id',
            'account_holder_name' => 'required',
            'bank_name'           => 'required',
            'account_number'      => 'required',
            'type'                => 'required',
            'ifsc'                => 'required'
        ];
    }
}
