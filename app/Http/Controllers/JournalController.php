<?php

namespace App\Http\Controllers;

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
}
