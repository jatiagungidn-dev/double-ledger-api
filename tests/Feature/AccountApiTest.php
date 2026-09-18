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

    public function test_authenticated_user_can_create_an_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/accounts', [
            'code' => '001',
            'name' => 'BCA',
            'type' => 'ASSET',
            'currency' => 'IDR',
        ]);

        $response->dump();

        $response->assertStatus(201)->assertJsonStructure([
            'status', 'message', 'data',
        ]);

        $this->assertDatabaseHas('accounts', [
            'code' => '001',
            'user_id' => $user->id,
        ]);
    }

    public function test_unauthenticated_user_rejects_create_an_account(): void
    {
        $response = $this->postJson('/api/accounts', [
            'code' => '001',
            'name' => 'BCA',
            'type' => 'ASSET',
            'currency' => 'IDR',
        ]);

        $response->dump();

        $response->assertStatus(401);
    }

    public function test_default_currency(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/accounts', [
            'code' => '001',
            'name' => 'BCA',
            'type' => 'ASSET',
        ]);

        $response->dump();

        $response->assertStatus(201)->assertJsonStructure([
            'status', 'message', 'data',
        ]);

        $this->assertDatabaseHas('accounts', [
            'code' => '001',
            'user_id' => $user->id,
            'currency' => 'IDR',
        ]);
    }

    public function test_unauthenticated_user_cannot_view_account(): void
    {
        $user = User::factory()->create();

        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $reponse = $this->getJson("/api/accounts/{$account->id}");

        $reponse->dump();

        $reponse->assertStatus(401);
    }

    public function test_account_requires_code(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/accounts', [
            'name' => 'BCA',
            'type' => 'ASSET',
        ]);

        $response->dump();

        $response->assertStatus(422)->assertJsonValidationErrors(['code']);
    }

    public function test_account_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/accounts', [
            'code' => '001',
            'type' => 'ASSET',
        ]);

        $response->dump();

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    public function test_account_requires_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/accounts', [
            'code' => '001',
            'name' => 'BCA',
        ]);

        $response->dump();

        $response->assertStatus(422)->assertJsonValidationErrors(['type']);
    }
}
