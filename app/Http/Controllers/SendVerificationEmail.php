<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Carbon;


class SendVerificationEmail extends Controller
{

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
    }

    /**
     * Send a verification email to the user after a valid registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendVerificationEmail(Request $request)
    {
        $data = $request->all();

        // Validate the request data
        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'email' => $data['email'],
            'verification_code' => Str::random(6), // 6桁のランダムな認証コードを生成して保存
            'verification_code_created_at' => Carbon::now(),
            'verification_code_cheaked' => true,
        ]);

        event(new Registered($user));

        Mail::to($user->email)->send(new VerificationCodeMail($user->verification_code)); // 認証コードを含むメールを送信

        return response()->json(['message' => 'Verification email sent successfully', 'user' => $user]);
    }
}
