<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <header></header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>user_name</th>
                    <th>email</th>
                    <th>住所</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach (($users ?? []) as $user)
                    <tr data-id="{{ $user['id'] ?? null }}" data-last_name="{{ $user['last_name'] ?? null }}" data-first_name="{{ $user['first_name'] ?? null }}">
                        <td>
                            <dl>
                                <dd>{{ $user["name"] ?? null }}</dd>
                                <dd>{{ $user["email"] ?? null }}</dd>
                            </dl>
                        </td>
                        <td>
                            <dl>
                                <dd>{{ $user["last_name"] ?? null }}</dd>
                                <dd>{{ $user["last_kana"] ?? null }}</dd>
                            </dl>
                        </td>
                        <td>
                            <dl>
                                <dd>{{ $user["first_name"] ?? null }}</dd>
                                <dd>{{ $user["first_kana"] ?? null }}</dd>
                            </dl>
                        </td>
                        <td>
                            <dl>
                                <dd>{{ $user["birthday"] ?? null }}</dd>
                            </dl>
                        </td>
                        <td>
                            <dl>
                                <dd>{{ $user->user_companies->count() ?? null }}</dd>
                            </dl>
                        </td>
                        <td>
                            <button onclick="location.href='{{ url('/users/'.($user['id'] ?? null))  }}'">詳細</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
    <footer></footer>
</body>
</html>
