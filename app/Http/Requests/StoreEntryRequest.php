<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'uuid', 'exists:accounts,id'],
            'journal_id' => ['required', 'uuid', 'exists:journals,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'type' => ['required', 'string', Rule::in(['DEBIT', 'CREDIT'])],
        ];
    }
}
