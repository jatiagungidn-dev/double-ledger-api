<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function show(Account $account): AccountResource
    {
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
