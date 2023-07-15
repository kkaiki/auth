<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function reset(Request $request)
    {
        // 入力バリデーション
        $request->validate([
            'email' => 'required|email',
            'old_password' => 'required',
            'password' => 'required|min:8',
            'password_confirm' => 'required|min:8|same:password',
        ]);

        // ユーザーの認証
        $user = User::where('email', $request->email)->first();

        // ユーザーが存在しない、または旧パスワードが一致しない場合はエラー
        if (!$user || !Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Invalid user or password.'], 422);
        }

        // 新しいパスワードをハッシュ化して保存
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password has been reset.']);
    }
}
