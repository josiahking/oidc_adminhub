<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OidcController;
use App\Http\Middleware\AuthenticateWithOidc;
use App\Http\Controllers\SubaccountController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/oidc/redirect', [OidcController::class, 'redirectToProvider'])
    ->name('oidc.redirect');

Route::get('/oidc/callback', [OidcController::class, 'handleProviderCallback'])
    ->name('oidc.callback');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/')->with('success', 'Logged out successfully.');
})->name('logout');

Route::middleware([AuthenticateWithOidc::class])->group(function () {
    Route::get('/dashboard', [SubaccountController::class, 'index'])->name('dashboard');
    Route::post('/subaccount/{organization}', [SubaccountController::class, 'openAsSubaccount'])->name('subaccount.open');
});