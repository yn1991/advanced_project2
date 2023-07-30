@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('ログイン') }}</div>

                <div class="card-body">
                    <form method="get" action="{{ route('login_form') }}">
                        @csrf

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('ログイン(2段階認証)') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="register">
        <p class="register_text">アカウントをお持ちでない方はこちらから</p>
        <a class="register_link" href="{{  route('getRegister') }}">会員登録</a>
    </div>

</div>
@endsection
