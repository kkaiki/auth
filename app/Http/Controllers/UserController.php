<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        // 入力バリデーション
        $request->validate([
            'email' => 'required|email',
        ]);

        // ユーザーの検索
        $user = User::where('email', $request->email)->first();

        // ユーザーが存在しない場合はエラー
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // ユーザーの名前とメールアドレスを返す
        return response()->json(['name' => $user->name, 'email' => $user->email]);
    }
}
