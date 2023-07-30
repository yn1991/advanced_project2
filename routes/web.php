<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//打刻ページ表示
Route::get('/', [AttendanceController::class, 'getindex'])->name('getindex')->middleware('auth');
//勤務開始処理
Route::get('/attendance/start', [AttendanceController::class, 'startAttendance'])->name('startAttendance')->middleware('auth');
//勤務終了処理
Route::get('/attendance/end', [AttendanceController::class, 'endAttendance'])->name('endAttendance')->middleware('auth');
//ページネーション
Route::get('/attendance', [AttendanceController::class, 'getAttendance'])->name('getAttendance')->middleware('auth');

//休憩開始処理
Route::get('/break/start', [RestController::class, 'startRest'])->name('startRest')->middleware('auth');
//休憩終了処理
Route::get('/break/end', [RestController::class, 'endRest'])->name('endRest')->middleware('auth');

//ユーザー新規登録ページ表示
Route::get('/register', [AuthController::class, 'getRegister'])->name('getRegister')->middleware('guest');
//ユーザー新規登録処理
Route::post('/register', [AuthController::class, 'postRegister'])->name('postRegister');

//ユーザーログインページ表示
Route::get('/login', [AuthController::class, 'getLogin'])->name('login')->middleware('guest');
//2段階認証
Route::get('ajax/two_factor_auth/', [TwoFactorAuthController::class, 'login_form'])->name('login_form');
Route::post('ajax/two_factor_auth/first_auth', [TwoFactorAuthController::class, 'first_auth'])->name('first_auth');
Route::post('ajax/two_factor_auth/second_auth', [TwoFactorAuthController::class, 'second_auth'])->name('second_auth');

//ユーザーログイン処理
Route::post('/login', [AuthController::class, 'postLogin'])->name('postLogin');

//ユーザーログアウト処理
Route::post('/logout', [AuthController::class, 'getLogout'])->name('getLogout')->middleware('auth');