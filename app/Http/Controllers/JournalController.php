<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJournalRequest;
use App\Http\Resources\JournalResource;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JournalController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $journals = $request->user()->journals()->latest('occurred_at')->paginate(10);

        return JournalResource::collection($journals);
    }

    public function show(Journal $journal): JournalResource
    {
        $this->authorize('view', $journal);

        return new JournalResource($journal);
    }

    public function store(StoreJournalRequest $request)
    {
        $data = $request->validated();

        if ($request->filled('idempotency_key')) {
            $existingJournal = $request->user()->journals()->where('idempotency_key', $data['idempotency_key'])->first();

            if ($existingJournal) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Journal already exists',
                    'data' => new JournalResource($existingJournal),
                ], 200);
            }
        }
        $journal = $request->user()->journals()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Journal created successfully',
            'data' => new JournalResource($journal),
        ], 201);
    }
}
