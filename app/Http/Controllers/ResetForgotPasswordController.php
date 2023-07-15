<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;

class ResetForgotPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // メールアドレスを検索
        $user = User::where('email', $request->email)->where('password_reset_token', hash('sha256', $request->token))->first();

        if (!$user || !$this->tokenIsValid($user)) {
            return response()->json(['message' => 'This password reset token is invalid.'], 422);
        }

        // パスワードリセット
        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Password has been reset.', 'user' => $user]);
    }

    private function tokenIsValid($user) {
        $expiration = Carbon::parse($user->password_reset_expires_at);
        return Carbon::now()->lessThan($expiration);
    }
}
