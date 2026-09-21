<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['account_id', 'journal_id', 'amount', 'type'];

    public function accounts(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function journals(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }
}
