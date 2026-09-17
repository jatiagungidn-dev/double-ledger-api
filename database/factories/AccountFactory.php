<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => fake()->unique()->numerify('ACC-####'),
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement([
                'ASSET', 'LIABILITY', 'EQUITY', 'REVENUE', 'EXPENSE',
            ]),
            'currency' => 'IDR',
        ];
    }
}
