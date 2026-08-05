<x-body.frame.admin>
    <x-slot name="name">home</x-slot>
    <x-slot name="title">TOP</x-slot>
    <x-slot name="head"></x-slot>
    <x-slot name="header">
    </x-slot>
    <x-slot name="page_transition_list"></x-slot>
    <x-slot name="main">
        <section>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menus as $menu)
                        <tr>
                            <td>{{ data_get($menu,"title") }}</td>
                            <td>{{ data_get($menu,"description") }}</td>
                            <td><button type="button" onclick="location.href='{{ url(data_get($menu,'name')) }}'">詳細</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @guest
            <section>
                <h3>ログイン画面</h3>
                <form action="{{ url('login') }}" method="post">
                    @csrf
                    <table>
                        <tbody>
                            <tr>
                                <th>メール</th>
                                <td><input id="email" type="email" name="email" value="{{ old('email') }}" required></td>
                            </tr>
                            <tr>
                                <th>パスワード</th>
                                <td><input id="password" type="password" name="password" required></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input id="remember_me" type="checkbox" name="remember">
                                    <label for="remember_me">ログイン状態を保持する</label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <button type="submit">ログイン</button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <a href="{{ asset('forgot-password') }}" style="border-bottom: 1px solid; font-size: small;">※パスワードを忘れた方はこちら</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </section>
        @endguest
        @auth
            <section>
                <h3>メニュー</h3>
                <ul>
                    <li><a href="{{ asset($user->name) }}">マイページ</a></li>
                    <li><a href="{{ asset('logout') }}">ログアウト</a></li>
                    <li><a href="{{ asset('daisuke') }}">daisuke</a></li>
                    <li><a href="https://hakoyasan.com">ウェブサイト</a></li>
                </ul>
            </section>
            <section>
            </section>

        @endauth
    </x-slot>
    <x-slot name="footer">
    </x-slot>
    <x-slot name="script"></x-slot>
    <x-slot name="hidden"></x-slot>
</x-body.frame.admin>
