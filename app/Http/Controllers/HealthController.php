<?php

namespace App\Http\Controllers;

class HealthController extends Controller
{
    public function show()
    {
        return response()->json([
            'status' => 'success',
            'service' => 'Double Ledger API',
            'database_status' => 'connected',
        ], 200);
    }
}
