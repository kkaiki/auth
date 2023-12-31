<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Carbon;

//パスワードをリセットするためのメールを送るAPI
class SendResetPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid email.'], 422);
        }

        // ユーザーが存在する場合は、パスワードリセット用のURLを生成し、メールで送信します
        $token = Str::random(60); // 60文字のランダムなトークンを生成
        $user->password_reset_token = hash('sha256', $token);
        $user->password_reset_expires_at = Carbon::now(); // 現在の時間を設定
        $user->save();

        // URL生成
        $url = URL::temporarySignedRoute(
            'reset-password', now()->addMinutes(60), ['token' => $token]
        );

        // メール送信
        Mail::to($request->email)->send(new ResetPasswordMail($url));

        return response()->json(['message' => 'Reset password link sent on your email id.', 'user' => $user]);
    }
}
