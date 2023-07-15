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
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User with this email does not exist.', 'email' => $request->email], 422);
        }

        if (!$this->tokenIsCorrect($user, $request->token)) {
            return response()->json(['message' => 'This password reset token is invalid.', 'token' => $request->token, 'user' => $user], 422);
        }

        // 有効期限切れの場合にエラーを返す
        if (!$this->tokenIsNotExpired($user)) {
            return response()->json(['message' => 'This password reset token has expired.', 'token' => $request->token], 422);
        }

        // パスワードリセット
        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Password has been reset.', 'user' => $user]);
    }

    private function tokenIsCorrect($user, $token) {
        return $token === $user->password_reset_token;
    }


    private function tokenIsNotExpired($user) {
        $expiration = Carbon::parse($user->password_reset_expires_at);
        return $expiration->diffInMinutes(Carbon::now()) <= 60;
    }
}
