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
        <section>
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
                </tbody>
            </table>
        </section>
        <section id="company-products-index">
            <form action="{{ url("/companies/".data_get($company,"code")) }}" method="POST">
                @csrf
                @method("post")
                <button type="submit">確認</button>
                <input type="hidden" name="company_code" value ="{{ data_get($company,"code") }}">
                <input type="hidden" name="company_name" value ="{{ data_get($company,"company_name") }}">
                <table>
                    <thead>
                        <tr>
                            <th>製品名</th>
                            <th>単価</th>
                            <th>数量</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($company_products as $company_product)
                            @php
                                // 共通となる name のベース文字列を作成
                                $company_code   = data_get($company_product, 'company_code');
                                $product_code   = data_get($company_product, 'product_code');
                                $base_name      = "company_products[{$company_code}][{$product_code}]";
                            @endphp
                        <tr>
                            <td>
                                <span>{{ data_get($company_product, "product_name", null) }}</span>
                                <input
                                    type="hidden"
                                    name="{{ $base_name }}[product_name]"
                                    value="{{ data_get($company_product, "product_name") }}"
                                >
                                <input
                                    type="hidden"
                                    name="{{ $base_name }}[custom_name]"
                                    value="{{ data_get($company_product, "custom_name") }}"
                                >
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="{{ $base_name }}[price]"
                                    step="0.1"
                                    value="{{ data_get($company_product, "price", null) }}"
                                >
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="{{ $base_name }}[quantity]"
                                    step="{{ data_get($company_product, "lot", 'any') }}"
                                >
                            </td>
                            <td>
                                <input
                                    type="date"
                                    name="{{ $base_name }}[date]"
                                >
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit">確認</button>
            </form>
            <button type="button" id="btn-unlock-step">限定解除</button>
        </section>
    </main>
    <footer></footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unlockBtn = document.getElementById('btn-unlock-step');

            unlockBtn.addEventListener('click', function () {
                // テーブル内のすべての number 型 input、または数量（.quantity-input）を対象にする
                const numberInputs = document.querySelectorAll('#company-products-index input[type="number"]');

                numberInputs.forEach(input => {
                    // step制限を解除（任意の数値・小数を許可）
                    input.setAttribute('step', 'any');
                });

                // フィードバック（視覚的に解除されたことを提示）
                unlockBtn.innerText = '限定解除済み';
                unlockBtn.disabled = true;
            });
        });
    </script>
</body>
</html>
