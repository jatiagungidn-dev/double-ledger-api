<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Entry;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entry>
 */
class EntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'journal_id' => Journal::factory(),
            'amount' => fake()->numerify('###.###'),
            'type' => fake()->randomElement(['DEBIT', 'CREDIT']),
        ];
    }
}
