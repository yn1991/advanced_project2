<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorAuthPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;

class TwoFactorAuthController extends Controller
{
    public function login_form() {  // ログインフォーム
        return view('two_factor_auth.login_form');
    }

    public function first_auth(Request $request) {  // １段階目の認証

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember'); // ユーザーが「Remember Me」を選択したかどうか

        if(\Auth::attempt($credentials, $remember)) {
            $random_password = '';
            for($i = 0 ; $i < 4 ; $i++) {
                $random_password .= strval(rand(0, 9));
            }

            $user = \App\Models\User::where('email', $request->email)->first();
            $user->tfa_token = $random_password;            // 4桁のランダムな数字
            $user->tfa_expiration = now()->addMinutes(10);  // 10分間だけ有効
            $user->save();

            // メール送信
            \Mail::to($user)->send(new TwoFactorAuthPassword($random_password));
            return [
                'result' => true,
                'user_id' => $user->id
            ];
        }
        return ['result' => false];
    }

    public function second_auth(Request $request) {  // ２段階目の認証

        $result = false;

        if($request->filled('tfa_token', 'user_id')) {
            $user = \App\Models\User::find($request->user_id);
            $expiration = new Carbon($user->tfa_expiration);

            if($user->tfa_token === $request->tfa_token && $expiration > now()) {
                $user->tfa_token = null;
                $user->tfa_expiration = null;
                $user->save();

                \Auth::login($user);    // 自動ログイン
                $result = true;
            }
        }
        return ['result' => $result];
    }
}
