<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_cannot_view_another_users_account(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $accountB = Account::factory()->create([
            'user_id' => $userB->id,
        ]);

        $response = $this->actingAs($userA, 'sanctum')->getJson("/api/accounts/{$accountB->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_view_own_account(): void
    {
        $user = User::factory()->create();

        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $reponse = $this->actingAs($user, 'sanctum')->getJson("/api/accounts/{$account->id}");

        $reponse->dump();

        $reponse->assertStatus(200);
    }
}
