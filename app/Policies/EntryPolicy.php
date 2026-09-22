<?php

namespace App\Policies;

use App\Models\Entry;
use App\Models\User;

class EntryPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user, Entry $entry): bool
    {
        return $user->id === $entry->journal->user_id && $user->id === $entry->account->user_id;
    }
}
