<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class SubaccountController extends Controller
{
    public function index()
    {
        $organizations = Organization::all();
        return view('dashboard', compact('organizations'));
    }

    public function openAsSubaccount(Request $request, Organization $organization)
    {
        // Stub for opening session as subaccount
        session(['current_organization' => $organization->id]);
        return response()->json([
            'status' => 'success',
            'message' => "Opened session as subaccount for {$organization->name}",
            'redirect' => route('dashboard'), // Optional: redirect after success
        ]);
    }
}
