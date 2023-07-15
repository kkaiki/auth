<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function authenticate(Request $request)
    {
        // 入力バリデーション
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirm' => 'required|min:8|same:password',
        ]);

        // ユーザーの認証
        $user = User::where('email', $request->email)->first();

        // ユーザーが存在しない、またはユーザーのverification_code_checkedが0の場合はエラー
        if (!$user) {
            return response()->json(['message' => 'Invalid user.'], 422);
        }

        if ($user->verification_code_checked == 0) {
            return response()->json(['message' => 'Not Authrize'], 422);
        }

        // 入力されたパスワードをハッシュ化して保存
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Authentication successful.']);
    }
}
