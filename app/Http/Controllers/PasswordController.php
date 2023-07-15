<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function authenticate(Request $request)
    {
        // ユーザーの認証
        $user = User::where('email', $request->email)->first();

        // ユーザーが存在しない場合はエラー
        if (!$user) {
            return response()->json(['message' => 'Invalid email.'], 422);
        }

        // パスワードが一致しない場合はエラー
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password.'], 422);
        }

        // 認証成功
        // ここで適切なレスポンスを返すか、必要な処理を実行します
        // 例えば、トークンの生成やセッションの作成など

        return response()->json(['message' => 'Authentication successful.']);
    }
}
