<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendVerificationEmail;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ResetForgotPasswordController;
use App\Http\Controllers\SendResetPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('api')->group(function () {
    // 既存のルート...

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset-password');
});
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/verify-email', [SendVerificationEmail::class, 'sendVerificationEmail']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/verify', [VerificationController::class, 'verify']);
Route::post('/login', [LoginController::class, 'login']);

Route::post('/password/email', [SendResetPasswordController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [ResetForgotPasswordController::class, 'reset']);
