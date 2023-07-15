<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // ユーザーが存在しない場合や認証コードが一致しない場合はエラー
        if (!$user || $user->verification_code !== $request->code) {
            return response()->json(['message' => 'Invalid code.'], 422);
        }

        // 認証コードが30分以上前に発行されていた場合はエラー
        if ($user->verification_code_created_at <= Carbon::now()->subMinutes(30)) {
            return response()->json(['message' => 'The code has expired.'], 422);
        }

        // 認証成功
        $user->email_verified_at = Carbon::now();
        $user->verification_code = null; // コードをクリア
        $user->verification_code_created_at = null; // コード発行時間をクリア
        $user->verification_code_checked = true;
        $user->save();

        return response()->json(['message' => 'Email verified successfully.', 'user' => $user]);
    }
}
