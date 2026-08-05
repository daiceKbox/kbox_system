<x-body.frame.admin>
    <x-slot name="name">companies</x-slot>
    <x-slot name="title">得意先一覧</x-slot>
    <x-slot name="head">
        <link rel="stylesheet" href="{{url("/css/companies.css")}}">
    </x-slot>
    <x-slot name="header">
    </x-slot>
    <x-slot name="page_transition_list"></x-slot>
    <x-slot name="main">
        <section id="companies-show">
            <table id="companies-show-table">
                <thead>
                    <tr>
                        <th class="companies-show-table-name">会社名</th>
                        <th class="companies-show-table-address">会社情報</th>
                        {{-- <th class="companies-show-table-action">操作</th> --}}
                    </tr>
                </thead>
                <tbody>
                    <tr data-row_index="{{ data_get($company,"row_index") }}" data-name="{{ data_get($company,"company_name") }}" data-kana="{{ data_get($company,"kana") }}">
                        <td class="companies-show-table-name">
                            <dl>
                                <dd class="companies-show-table-name-company_name">{{ data_get($company, "company_name") }}</dd>
                            </dl>
                            <dl>
                                <dd class="companies-show-table-name-kana">{{ data_get($company, "kana") }}</dd>
                            </dl>
                        </td>
                        <td class="companies-show-table-address">
                            <dl>
                                <dd class="companies-show-table-address-address">{!! nl2br(e(data_get($company, "address.address"))) !!}</dd>
                            </dl>
                            <dl>
                                <dd class="companies-show-table-address-tel">TEL:{{ data_get($company, "contact.tel") }}</dd>
                                <dd class="companies-show-table-address-fax">FAX:{{ data_get($company, "contact.fax") }}</dd>
                            </dl>
                        </td>
                        {{-- <td class="companies-show-table-action">
                            <button onclick="location.href='{{ url('/companies/'.data_get($company, 'code')) }}'">詳細</button>
                        </td> --}}
                    </tr>
                </tbody>
            </table>
        </section>
        <section id="companies-company_products">
            <form action="{{ url("/companies/".data_get($company,"code")) }}" method="POST">
                @csrf
                @method("post")
                <button type="submit">確認</button>
                <input type="hidden" name="company_code" value ="{{ data_get($company,"code") }}">
                <input type="hidden" name="company_name" value ="{{ data_get($company,"company_name") }}">
                <table id="companies-company_products-table">
                    <thead>
                        <tr>
                            <th class="companies-company_products-table-name">製品名</th>
                            <th class="companies-company_products-table-pirce">単価</th>
                            <th class="companies-company_products-table-quantity">数量</th>
                            <th class="companies-company_products-table-deadline">納期</th>
                            <th class="companies-company_products-table-order_number">注番</th>
                            <th class="companies-company_products-table-memo">メモ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($company_products as $company_product)
                            @php
                                // 共通となる name のベース文字列を作成
                                $company_code   =   data_get($company_product, 'company_code');
                                $product_code   =   data_get($company_product, 'product_code');
                                $base_name      =   "company_products[{$company_code}][{$product_code}]";
                            @endphp
                        <tr>
                            <td class="companies-company_products-table-name">
                                <dl>
                                    <dd>{{ data_get($company_product, "product_name") }}</dd>
                                </dl>
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
                            <td class="companies-company_products-table-pirce">
                                <input
                                    class="companies-company_products-table-price-input"
                                    type="number"
                                    name="{{ $base_name }}[price]"
                                    step="0.1"
                                    value="{{ number_format((float) data_get($company_product, "price", null), 2, ".", "") }}"
                                >
                            </td>
                            <td class="companies-company_products-table-quantity">
                                <input
                                    class="companies-company_products-table-quantity-input"
                                    type="number"
                                    name="{{ $base_name }}[quantity]"
                                    step="{{ data_get($company_product, "lot", 'any') }}"
                                >
                            </td>
                            <td class="companies-company_products-table-deadline">
                                <input
                                    class="companies-company_products-table-deadline-input"
                                    type="date"
                                    name="{{ $base_name }}[deadline]"
                                    value="{{ data_get($working_date,"next_date") }}"
                                >
                            </td>
                            <td class="companies-company_products-table-order_number">
                                <input
                                    class="companies-company_products-table-order_number-input"
                                    type="text"
                                    name="{{ $base_name }}[order_number]"
                                    placeholder="注番"
                                >
                            </td>
                            <td class="companies-company_products-table-memo">
                                <input
                                    class="companies-company_products-table-memo-input"
                                    type="text"
                                    name="{{ $base_name }}[memo]"
                                    placeholder="メモ"
                                >
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="companies-company_products-table-title" colspan="2">一括上書き</td>
                            <td class="companies-company_products-table-quantity">
                                <input
                                    class="companies-company_products-table-quantity-input"
                                    type="number"
                                    name="apply_bulk_update[quantity]"
                                    onchange="apply_bulk_update(this);"
                                >
                            </td>
                            <td class="companies-company_products-table-deadline">
                                <input
                                    class="companies-company_products-table-deadline-input"
                                    type="date"
                                    name="apply_bulk_update[deadline]"
                                    onchange="apply_bulk_update(this);"
                                >
                            </td>
                            <td class="companies-company_products-table-order_number">
                                <input
                                    class="companies-company_products-table-order_number-input"
                                    type="text"
                                    name="apply_bulk_update[order_number]"
                                    placeholder="注番"
                                    onchange="apply_bulk_update(this);"
                                >
                            </td>
                            <td class="companies-company_products-table-memo">
                                <input
                                    class="companies-company_products-table-memo-input"
                                    type="text"
                                    name="apply_bulk_update[memo]"
                                    placeholder="メモ"
                                    onchange="apply_bulk_update(this);"
                                >
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <button type="submit">確認</button>
            </form>
            <button type="button" id="btn-unlock-step">限定解除</button>
        </section>
    </x-slot>
    <x-slot name="footer">
    </x-slot>
    <x-slot name="script">
        <script>
            function apply_bulk_update(node){
                const value         =   node.value;
                const class_name    =   node.className;
                if(value == "") return;
                const table =   node.closest("table");
                if(! table) return;
                table.querySelectorAll(`tbody > tr > td > input.${class_name}`).forEach(input => {
                    // 値の更新
                        input.value = value;

                    // tbody側の再計算イベント等を発火させる
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                node.value  =   "";
            }



            document.addEventListener('DOMContentLoaded', function () {
                const unlockBtn = document.getElementById('btn-unlock-step');

                unlockBtn.addEventListener('click', function () {
                    // テーブル内のすべての number 型 input、または数量（.quantity-input）を対象にする
                    const numberInputs = document.querySelectorAll('#company-products-show input[type="number"]');

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
    </x-slot>
    <x-slot name="hidden"></x-slot>
</x-body.frame.admin>
</body>
</html>
