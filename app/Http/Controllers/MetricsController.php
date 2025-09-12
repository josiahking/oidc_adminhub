<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'ok',
            'metrics' => [
                'active_users' => \App\Models\User::count(),
                'organizations' => \App\Models\Organization::count(),
                'timestamp' => now()->toIso8601String(),
            ]
        ]);
    }
}
