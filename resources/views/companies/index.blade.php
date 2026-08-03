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
                    <th>会社名</th>
                    <th>カナ</th>
                    <th>住所</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($companies as $company)
                    <tr data-row_index="{{ $company['row_index'] ?? null }}" data-name="{{ $company['name'] ?? null }}" data-kana="{{ $company['kana'] ?? null }}">
                        <td>{{ $company["company_name"] ?? null }}</td>
                        <td>{{ $company["kana"] ?? null }}</td>
                        <td>
                            <dl>
                                <dd>{{ $company["address"]["address"] ?? null }}</dd>
                            </dl>
                            <dl style="display: flex">
                                <dd>{{ $company["contact"]["tel"] ?? null }}</dd>
                                <dd>{{ $company["contact"]["fax"] ?? null }}</dd>
                            </dl>
                        </td>
                        <td>
                            <button onclick="location.href='{{ url('/companies/'.($company['code'] ?? null))  }}'">詳細</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
    <footer></footer>
</body>
</html>
