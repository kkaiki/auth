<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{
    public function get(Request $request)
    {
        // 入力からメールアドレスを取得
        $email = $request->input('email');

        // ユーザーを検索
        $user = User::where('email', $email)->first();

        // ユーザーが存在しない場合はエラー
        if (!$user) {
            return response()->json(['message' => 'ユーザーが見つかりません。'], 404);
        }

        // ユーザーの名前とメールアドレスを返す
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
