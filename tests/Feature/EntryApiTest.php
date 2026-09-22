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

    /**
     * A basic feature test example.
     */
    public function test_user_can_get_an_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $account = Account::factory()->create(['user_id' => $user->id]);
        $entry = Entry::factory()->create(['account_id' => $account->id, 'journal_id' => $journal->id]);

        $response = $this->actingAs($user)->getJson("/api/entries/{$entry->id}");
        $response->dump();
        $response->assertStatus(200);
    }
}
