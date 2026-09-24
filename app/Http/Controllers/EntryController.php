<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntryRequest;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    public function index(Journal $journal): AnonymousResourceCollection
    {
        $this->authorize('view', $journal);

        $entries = $journal->entries()->with('account')->latest()->get();

        return EntryResource::collection($entries);
    }

    public function show(Entry $entry): EntryResource
    {
        $this->authorize('view', $entry->journal);

        return new EntryResource($entry->load('account'));
    }

    public function store(StoreEntryRequest $request, Journal $journal): JsonResponse
    {
        $validated = $request->validated();
        $journal = Journal::findOrFail($validated['journal_id']);

        $this->authorize('update', $journal);

        $entry = DB::transaction(function () use ($validated) {
            return Entry::create($validated);
        });

        return (new EntryResource($entry->load('account')))->response()->setStatusCode(201);
    }

    public function destroy(Entry $entry): JsonResponse
    {
        $this->authorize('update', $entry->journal);

        DB::transaction(function () use ($entry) {
            $entry->delete();
        });

        return response()->json([
            'message' => 'Entry deleted successfully',
        ], 200);
    }
}
