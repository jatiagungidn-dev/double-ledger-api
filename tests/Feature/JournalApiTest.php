<?php

namespace Tests\Feature;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class JournalApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_get_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/journals/{$journal->id}");

        $response->assertStatus(200);
    }

    public function test_get_all_journals(): void
    {
        $user = User::factory()->create();

        Journal::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/journals');

        $response->assertStatus(200);
    }

    public function test_user_can_create_journal_successfully(): void
    {
        $user = User::factory()->create();

        $payload = [
            'description' => 'Opening Balance Transfer',
            'occurred_at' => now()->toIso8601String(),
        ];

        $response = $this->actingAs($user)->postJson('/api/journals', $payload, ['X-Idempotency-Key' => (string) Str::uuid()]);

        $response->assertStatus(201)->assertJsonStructure([
            'status', 'message', 'data',
        ]);
        $response->dump();

        $this->assertDatabaseHas('journals', [
            'user_id' => $user->id,
            'description' => 'Opening Balance Transfer',
        ]);
    }

    public function test_create_journal_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/journals', []);
        $response->dump();

        $response->assertStatus(422)->assertJsonValidationErrors(['description', 'occurred_at']);
    }

    public function test_journal_creation_is_idempotent(): void
    {
        $user = User::factory()->create();
        $idempotencyKey = Str::uuid();

        $payload = [
            'description' => 'Payment for invoice #101',
            'occurred_at' => now()->toIso8601String(),
        ];

        $response = $this->actingAs($user)->postJson('/api/journals', $payload, ['X-Idempotency-Key' => $idempotencyKey]);
        $response->dump();
        $response->assertStatus(201);

        $this->assertDatabaseCount('journals', 1);
    }

    public function test_unauthenticated_user_cannot_create_journal(): void
    {
        $payload = [
            'description' => 'Unauthorized journal',
            'occurred_at' => now()->toIso8601String(),
        ];

        $response = $this->postJson('/api/journals', $payload);

        $response->assertStatus(401);
    }
}
