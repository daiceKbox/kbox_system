<x-body.frame.admin>
    <x-slot name="name">orders</x-slot>
    <x-slot name="title">受注</x-slot>
    <x-slot name="head"></x-slot>
    <x-slot name="header"></x-slot>
    <x-slot name="page_transition_list"></x-slot>
    <x-slot name="main">
        {{-- <section id="orders-index-query">
            <h3>絞り込み</h3>
            <x-orders.table-hiraganas id="orders-index-query-table" function_name="narrow_down_companies(this);"/>
        </section> --}}
        <section id="orders-index-query">
            <h3>表示条件</h3>
            <form action="" name="orders-index-query" onchange="narrow_down_orders(this);">
                <table id="orders-index-query-table">
                    <tbody>
                        <tr>
                            <th colspan="3">受注日</th>
                            <th>得意先</th>
                            <th>表示条件</th>
                        </tr>
                        <tr>
                            <td><input type="date" name="query[date][start_date]" value="{{ now()->format("Y-m-d") }}"></td>
                            <td><span>～</span></td>
                            <td><input type="date" name="query[date][end_date]" value="{{ now()->format("Y-m-d") }}"></td>
                            <td><input type="text" name="query[company]" placeholder="会社名 または コード"></td>
                            <td>
                                <select name="query[display]">
                                    <option value="all" selected>すべて</option>
                                    <option value="quantity">数量未定</option>
                                    <option value="price">単価未定</option>
                                    <option value="deadline">納期未定</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3">納期</th>
                            <th>製品</th>
                            <th>並べ替え</th>
                        </tr>
                        <tr>
                            <td><input type="date" name="query[deadline][start_date]"></td>
                            <td><span>～</span></td>
                            <td><input type="date" name="query[deadline][end_date]"></td>
                            <td><input type="text" name="query[product]" placeholder="製品名 または コード"></td>
                            <td>
                                <select name="query[order_by]">
                                    <option value="id" selected>受注番号順</option>
                                    <option value="company_code">会社コード</option>
                                    <option value="company_name">会社名</option>
                                    <option value="product_code">製品コード</option>
                                    <option value="product_name">製品名</option>
                                    <option value="deadline">納期順</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </section>
        <section id="orders-index">
            <h3>受注一覧</h3>
            <table id="orders-index-total-table">
                <thead>
                    <tr>
                        <th>数量</th>
                        <th>売上</th>
                    </tr>
                    <tr>
                        <td><span  id="orders-index-total-table-quantity">0</span>個</td>
                        <td><span  id="orders-index-total-table-sales">0</span>円</td>
                    </tr>
                </thead>
            </table>

            <form action="{{ route("orders.store") }}" method="POST" onsubmit="prepare_submit(this);">
                @csrf
                @method("post")
                <p>
                    <button type="submit">変更</button>
                    <button type="button" onclick="set_previous_value();">元に戻す</button>
                    <button type="button" onclick="change_voucher_mode();">伝票</button>
                </p>
                <table id="orders-index-table">
                    <thead>
                        <tr>
                            <th class="orders-index-table-id">ID</th>
                            <th class="orders-index-table-date">受注日</th>
                            <th class="orders-index-table-company" colspan="2">会社</th>
                            <th class="orders-index-table-product" colspan="2">製品</th>
                            <th class="orders-index-table-voucher hidden">伝票記載名</th>
                            <th class="orders-index-table-quantity">数量</th>
                            <th class="orders-index-table-price">単価</th>
                            <th class="orders-index-table-deadline">納期</th>
                            <th class="orders-index-table-voucher  hidden">伝票</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach (($orders ?? []) as $order)
                            <tr
                                class="{{ data_get($order,"date") == now()->format("Y/m/d") ? "" : "hidden" }}"
                                data-row_index="{{ data_get($order,"row_index") }}"
                                data-id="{{ data_get($order,"id") }}"
                                data-date="@date(data_get($order,"date"))"
                                data-company_code="{{ (string) data_get($order,"company_code") }}"
                                data-company_name="{{ (string) data_get($order,"company_name") }}"
                                data-product_code="{{ (string) data_get($order,"product_code") }}"
                                data-product_name="{{ (string) data_get($order,"product_name") }}"
                                data-quantity="@num(data_get($order,"quantity"))"
                                data-price="@price(data_get($order,"price"))"
                                data-deadline="@date(data_get($order,"deadline"))"
                            >
                                <td class="orders-index-table-company-id">{{ data_get($order, "id") }}</td>
                                <td class="orders-index-table-company-date">{{ data_get($order, "date") }}</td>
                                <td class="orders-index-table-company-company_code">{{ data_get($order, "company_code") }}</td>
                                <td class="orders-index-table-company-company_name">{{ data_get($order, "company_name") }}</td>
                                <td class="orders-index-table-product-product_code">{{ data_get($order, "product_code") }}</td>
                                <td class="orders-index-table-product-product_name">{{ data_get($order, "product_name") }}</td>
                                <td class="orders-index-table-voucher hidden">{{ data_get($order, "custom_name") }}</td>
                                <td class="orders-index-table-quantity">
                                    <input
                                        class="orders-index-table-quantity-input"
                                        type="number"
                                        name="orders[{{ (string)data_get($order, "id") }}][quantity]"
                                        value="@num(data_get($order, "quantity"))"
                                        data-previous_value="@num(data_get($order, "quantity"))"
                                        onchange="is_changed(this);"
                                    >

                                </td>
                                <td class="orders-index-table-price">
                                    <input
                                        class="orders-index-table-price-input"
                                        type="number"
                                        name="orders[{{ (string)data_get($order, "id") }}][price]"
                                        step="0.1"
                                        value="@price(data_get($order, "price"))"
                                        data-previous_value="@price(data_get($order, "price"))"
                                        onchange="is_changed(this);"
                                    >
                                </td>
                                <td class="orders-index-table-deadline">
                                    <input
                                        class="orders-index-table-deadline-input"
                                        type="date"
                                        name="orders[{{ (string)data_get($order, "id") }}][deadline]"
                                        value="@date(data_get($order, "deadline"))"
                                        data-previous_value="@date(data_get($order, "deadline"))"
                                        onchange="is_changed(this);"
                                    >
                                </td>
                                <td class="orders-index-table-voucher hidden">
                                    <input
                                        class="orders-index-table-voucher-input"
                                        type="hidden"
                                        name="orders[{{ (string)data_get($order, "id") }}][status.voucher]"
                                        value="pending"
                                        data-previous_value="{{ data_get($order, "status.voucher")}}"
                                        disabled
                                    >
                                    <input
                                        class="orders-index-table-voucher-input"
                                        type="checkbox"
                                        name="orders[{{ (string)data_get($order, "id") }}][status.voucher]"
                                        @checked(data_get($order, "status.voucher") == "completed")
                                        value="completed"
                                        data-previous_value="{{ data_get($order, "status.voucher")}}"
                                        onchange="is_changed(this);"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
        </section>
    </x-slot>
    <x-slot name="footer">
    </x-slot>
    <x-slot name="script">
        <script>
            function is_changed(node) {
                const class_name        =   "orders-index-table-input-changed";
                let judgment            =   false;

                const value             =   node.value                  ?? "";
                const previous_value    =   node.dataset.previous_value ?? "";
                switch(node.type){
                    case("number"):
                        // 文字列の数値としてのブレ（例: "100" と "100.00"）を避けるため float に変換して比較
                        const num_value             =   parseFloat(value) || 0;
                        const num_previous_value    =   parseFloat(previous_value) || 0;
                        judgment    =   num_value !== num_previous_value;
                        break;
                    case("checkbox"):
                        const checked       =   node.checked;
                        const pending       =   document.querySelector("input[name='"+node.name+"'][type='hidden']");
                        pending.disabled    =   checked;
                        pending.classList.toggle(class_name);
                    case("text"):
                    case("date"):
                    default:
                        judgment    =   value.trim() !== previous_value.trim();
                }

                // 変更されているかどうかでクラスをトグル
                node.classList.toggle(class_name, judgment);
            }

            function set_previous_value(){
                const inputs   =   document.querySelectorAll("input:not([name='_token'])");
                inputs.forEach(input=>{
                    input.value = input.dataset.previous_value;
                    input.classList.remove("orders-index-table-input-changed");
                });
            }

            function change_voucher_mode(){
                const vouchers  =   document.querySelectorAll(".orders-index-table-voucher");
                vouchers.forEach(voucher => voucher.classList.toggle("hidden"));
            }

            function prepare_submit(node){
                const inputs   =   node.querySelectorAll("input:not([name='_token'])");
                inputs.forEach(input=>input.disabled = input.classList.contains("orders-index-table-input-changed") == false);
            }

            narrow_down_orders(document.querySelector("form[name=orders-index-query]"));
            function narrow_down_orders(node) {
                let total_quantity  =   0;
                let total_sales     =   0;

                const form = node.closest('form') || document;
                // 1. 検索フォームの入力値を取得
                    const start_date            = form.querySelector('[name="query[date][start_date]"]').value;
                    const end_date              = form.querySelector('[name="query[date][end_date]"]').value;
                    const deadline_start_date   = form.querySelector('[name="query[deadline][start_date]"]').value;
                    const deadline_end_date     = form.querySelector('[name="query[deadline][end_date]"]').value;
                    const company               = form.querySelector('[name="query[company]"]').value.trim().toLowerCase();
                    const product               = form.querySelector('[name="query[product]"]').value.trim().toLowerCase();
                    const display               = form.querySelector('[name="query[display]"]').value;  // all, quantity, price, deadline
                    const order_by              = form.querySelector('[name="query[order_by]"]').value; // id, company_code, etc.


                // 2. 一覧の行を取得してループ
                    const tbody = document.querySelector('table#orders-index-table > tbody');
                    if (!tbody) return;
                    const rows = Array.from(tbody.querySelectorAll("tr"));

                    rows.forEach(row => {
                        const row_date      =   row.dataset.date                    || '';
                        const row_deadline  =   row.dataset.deadline                || '';
                        const row_company   =   (row.dataset.company_name           || '').toLowerCase()
                                            +   (row.dataset.company_code           || '').toLowerCase();
                        const row_product   =   (row.dataset.product_name           || '').toLowerCase()
                                            +   (row.dataset.product_code           || '').toLowerCase();
                        const row_quantity  =   parseFloat(row.dataset.quantity)    || 0;
                        const row_price     =   parseFloat(row.dataset.price)       || 0;

                        let judgment = true;
                        // --- 条件判定 ---
                        // ① 受注日（開始～終了）
                            if(start_date)  judgment    = judgment  ? row_date >= start_date : false;
                            if(end_date)    judgment    = judgment  ? row_date <= end_date  : false;

                        // ② 納期（開始～終了）
                            if(deadline_start_date) judgment    = judgment  ? row_deadline >= deadline_start_date   : false;
                            if(deadline_end_date)   judgment    = judgment  ? row_deadline <= deadline_end_date     : false;

                        // ③ 会社名またはコード（部分一致）
                        // ④ 製品名またはコード（部分一致）
                            if(company) judgment    = judgment  ? row_company.includes(company) : false;
                            if(product) judgment    = judgment  ? row_product.includes(product) : false;

                        // // ⑤ 表示条件（未定系のフィルター）
                            if(display == "quantity")   judgment    = judgment ? !row_quantity  : false;
                            if(display == "price")      judgment    = judgment ? !row_price     : false;
                            if(display == "deadline")   judgment    = judgment ? !row_deadline  : false;

                        // 判定結果に応じて hidden クラスをトグル
                            row.classList.toggle('hidden', !judgment);
                            if(judgment){
                                total_quantity  +=  row_quantity;
                                total_sales     +=  row_quantity * row_price;
                            }
                    });

                    document.querySelector("#orders-index-total-table-quantity").innerHTML   = total_quantity.toLocaleString();
                    document.querySelector("#orders-index-total-table-sales").innerHTML      = total_sales.toLocaleString();

                // // ⑥ 並べ替え（ソート）の実行
                //     rows.sort((a,b)=>{
                //         let val_a   =   a.dataset[order_by] || "";
                //         let val_b   =   b.dataset[order_by] || "";
                //         return val_a.localeCompare(val_b, "ja");
                //     });

                //     rows.forEach(row => tbody.appendChild(row));
            }
        </script>
        {{-- <x-orders.script-hiraganas function_name="narrow_down_companies(node)" table_id="orders-index-table" /> --}}
    </x-slot>
    <x-slot name="hidden"></x-slot>
</x-body.frame.admin>
