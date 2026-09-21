<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEntryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_id' => ['reqired', 'uuid', 'exists:accounts,id'],
            'journal_id' => ['reqired', 'uuid', 'exists:journals,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'type' => ['required', 'string', Rule::in(['DEBIT', 'CREDIT'])],
        ];
    }
}
