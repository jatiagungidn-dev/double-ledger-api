<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntryRequest;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Models\Journal;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EntryController extends Controller
{
    public function index(Journal $journal): AnonymousResourceCollection
    {
        $this->authorize('view', $journal);

        return EntryResource::collection($journal->entries()->latest()->paginate(10));
    }

    public function show(Entry $entry): EntryResource
    {
        $this->authorize('view', $entry);

        return new EntryResource($entry);
    }

    public function store(StoreEntryRequest $request, Journal $journal): EntryResource
    {
        $this->authorize('view', $journal);

        $data = $request->validated();

        $account = $request->user()->accounts()->findOrFail($data['account_id']);

        $entry = $journal->entries()->create([
            'account_id' => $account->id,
            'amount' => $data['amount'],
            'type' => $data['type'],
        ]);

        return new EntryResource($entry);
    }
}
