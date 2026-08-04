<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KBOX SYSTEM</title>
</head>
<body>
    <header></header>
    <main>
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
    </main>
    <footer></footer>
</body>
</html>
