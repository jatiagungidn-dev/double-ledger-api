<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Entry;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_an_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $account = Account::factory()->create(['user_id' => $user->id]);
        $entry = Entry::factory()->create(['account_id' => $account->id, 'journal_id' => $journal->id]);

        $response = $this->actingAs($user)->getJson("/api/entries/{$entry->id}");
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_user_entries(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $journal = Journal::factory()->create(['user_id' => $userA->id]);
        $account = Account::factory()->create(['user_id' => $userA->id]);
        $entry = Entry::factory()->create(['account_id' => $account->id, 'journal_id' => $journal->id]);

        $response = $this->actingAs($userB, 'sanctum')->getJson("/api/entries/{$entry->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $account = Account::factory()->create(['user_id' => $user->id]);
        $entry = Entry::factory()->create(['account_id' => $account->id, 'journal_id' => $journal->id]);

        $response = $this->actingAs($user)->deleteJson("/api/entries/{$entry->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('entries', [
            'id' => $entry->id,
        ]);
    }

    public function test_user_cannot_delete_other_user_entries(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $journal = Journal::factory()->create(['user_id' => $userA->id]);
        $account = Account::factory()->create(['user_id' => $userA->id]);
        $entry = Entry::factory()->create(['account_id' => $account->id, 'journal_id' => $journal->id]);

        $response = $this->actingAs($userB, 'sanctum')->deleteJson("/api/entries/{$entry->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('entries', [
            'id' => $entry->id,
        ]);
    }

    public function test_journal_must_be_balanced_between_debit_and_credit(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $account1 = Account::factory()->create(['user_id' => $user->id]);
        $account2 = Account::factory()->create(['user_id' => $user->id]);

        Entry::factory()->create([
            'journal_id' => $journal->id,
            'account_id' => $account1->id,
            'amount' => 500000,
            'type' => 'DEBIT',
        ]);

        Entry::factory()->create([
            'journal_id' => $journal->id,
            'account_id' => $account2->id,
            'amount' => 300000,
            'type' => 'CREDIT',
        ]);

        $this->assertFalse($journal->fresh()->isBalanced());

        Entry::factory()->create([
            'journal_id' => $journal->id,
            'account_id' => $account2->id,
            'amount' => 200000,
            'type' => 'CREDIT',
        ]);

        $this->assertTrue($journal->fresh()->isBalanced());
    }
}
