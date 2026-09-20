<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = $request->user()->accounts()->paginate(10);

        return AccountResource::collection($accounts);
    }

    public function show(Account $account): AccountResource
    {
        $this->authorize('view', $account);

        return new AccountResource($account);
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['currency'] = $data['currency'] ?? 'IDR';

        $account = $request->user()->accounts()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Account created successfully',
            'data' => new AccountResource($account),
        ], 201);
    }
}
