<html>
<head>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div id="app" class="p-5 form-body">
        <div class="alert alert-info" v-if="message" v-text="message"></div>

        <!-- １段階目のログインフォーム -->
        <div v-if="step==1" >
            <div class="form-group">
                <label>メールアドレス</label>
                <input type="text" class="form-control" v-model="email">
            </div>
            <div class="form-group">
                <label>パスワード</label>
                <input type="password" class="form-control" v-model="password">
            </div>
            <div class="row mb-3">
                <div class="col-md-6 offset-md-4 form-check-area">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('ログイン状態を記憶') }}
                        </label>
                    </div>
                    <button type="button" class="btn btn-primary" @click="firstAuth">送信する</button>
                </div>
            </div>
        </div>

        <!-- ２段階目・ログインフォーム -->
        <div v-if="step==2">
            ２段階認証のパスワードをメールアドレスに登録しました。（有効時間：10分間）
            <hr>
            <div class="form-group">
                <label>２段階パスワード</label>
                <input type="text" class="form-control" v-model="token">
            </div>
            <button type="button" class="btn btn-primary" @click="secondAuth">送信する</button>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script>

        new Vue({
            el: '#app',
            data: {
                step: 1,
                email: '',
                password: '',
                token: '',
                userId: -1,
                message: ''
            },
            methods: {
                firstAuth() {

                    this.message = '';

                    const url = '/ajax/two_factor_auth/first_auth';
                    const params = {
                        email: this.email,
                        password: this.password
                    };
                    axios.post(url, params)
                        .then(response => {

                            const result = response.data.result;

                            if(result) {

                                this.userId = response.data.user_id;
                                this.step = 2;

                            } else {

                                this.message = 'ログイン情報が間違っています。';

                            }

                        });

                },
                secondAuth() {

                    const url = '/ajax/two_factor_auth/second_auth';
                    const params = {
                        user_id: this.userId,
                        tfa_token: this.token
                    };

                    axios.post(url, params)
                        .then(response => {

                            const result = response.data.result;

                            if(result) {

                                // ２段階認証成功
                                location.href = '/';

                            } else {

                                this.message = '２段階パスワードが正しくありません。';
                                this.token = '';

                            }

                        });

                }
            }
        });

    </script>
</body>
</html>