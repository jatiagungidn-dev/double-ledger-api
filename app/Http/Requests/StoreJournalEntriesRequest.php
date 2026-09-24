<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJournalEntriesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entries' => ['required', 'array', 'min:2'],
            'entries.*.account_id' => ['required', 'uuid', 'exists:accounts,id'],
            'entries.*.amount' => ['required', 'numeric', 'gt:0'],
            'entries.*.type' => ['required', 'string', 'in:DEBIT,CREDIT'],
        ];
    }

    public function withValidatio($validator)
    {
        $validator->after(function ($validator) {
            $entries = collect($this->input('entries', []));

            $totalDebit = $entries->where('type', 'DEBIT')->sum('amount');
            $totalCredit = $entries->where('type', 'CREDIT')->sum('amount');

            if (abs($totalDebit - $totalCredit) > 0.0001) {
                $validator->errors()->add('entries', "Total DEBIT ({$totalDebit}) must be balance with total CREDIT ({$totalCredit})");
            }
        });
    }
}
