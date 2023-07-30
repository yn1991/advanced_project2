<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{        
    public function __construct()
    {
 
    }

    public function getRegister()
    {
        return view('register');
    }

    public function postRegister(Request $request)
    {
        $RegisterController = new RegisterController;
        $RegisterController->register($request);
        return view('login');
    }

    public function getLogin()
    {
        return view('login');
    }

    public function postLogin(Request $request)
    {   
         // バリデーションルールの定義
        $rules = [
            'email' => 'required|email|string|max:191',
            'password' => 'required|max:191|min:8',
        ];

        // カスタムエラーメッセージの定義
        $errorMessages = [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.string' => 'メールアドレスは文字列である必要があります。',
            'email.max' => 'メールアドレスは191文字以内で入力してください。',
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上入力してください。',
            'password.max' => 'パスワードは191文字以内で入力してください。',
        ];

        // バリデーションの実行
        $validator = Validator::make($request->all(), $rules, $errorMessages);

        // バリデーションエラーがある場合
        if ($validator->fails()) {
            // エラーメッセージを取得して処理する
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // バリデーションが成功した場合の処理
        $credentials = $request->only('email', 'password');
        $email = $credentials['email']; // メールアドレスを取得
        $remember = $request->has('remember'); // ユーザーが「Remember Me」を選択したかどうか

        if (Auth::attempt($credentials, $remember))
        {
        //$LoginController = new LoginController;
        //$LoginController->login($request);

        $attendanceStarted = Session::get('attendanceStarted', false);
        $restStarted = Session::get('restStarted', false);

        return redirect('/')->with(['attendanceStarted' => $attendanceStarted, 'restStarted' => $restStarted]);
        } else {
            // ログイン失敗の処理
            return redirect()->back()->withErrors(['auth' => 'メールアドレスまたはパスワードが正しくありません。'])->withInput();
        }
    }

    public function getLogout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
