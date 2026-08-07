<x-body.frame.admin>
    <x-slot name="name">vouchers</x-slot>
    <x-slot name="title">伝票</x-slot>
    <x-slot name="head"></x-slot>
    <x-slot name="header"></x-slot>
    <x-slot name="page_transition_list"></x-slot>
    <x-slot name="main">
        {{-- <section id="voucher-index-query">
            <h3>絞り込み</h3>
            <x-voucher.table-hiraganas id="voucher-index-query-table" function_name="narrow_down_companies(this);"/>
        </section> --}}
        <section id="voucher-index-query">
            <h3>納期を選ぶ</h3>
            <form action="" name="voucher-index-query" onchange="narrow_down_orders(this);" method="GET">
                @csrf
                @method("get")
                <p>
                    <input type="date" name="deadline" value="{{ $deadline ?? now()->format("Y-m-d") }}">
                    <button type="submit">取得</button>
                </p>
            </form>
        </section>
        <section id="voucher-index">
            <h3>伝票</h3>
            <p>
                <select>
                    <option value="all" selected>すべて</option>
                    <option value="unfilled">未入力のみ</option>
                    <option value="filled">入力済みのみ</option>
                </select>
            </p>
            <ul>
                @foreach ($orders as $key => $grouped_orders)
                    @php
                        $sumple_order   =   $grouped_orders[0]   ??  [];
                    @endphp
                        <li>
                            <div>
                                <p><span>{{ data_get($sumple_order, "company_name") }}</span>御中</p>
                                <p>令和<span>{{ explode('-', $deadline)[0] -2018 ?? "" }}</span>年<span>{{ explode('-', $deadline)[1] ?? "" }}</span>月<span>{{ explode('-', $deadline)[2] ?? "" }}</span>日</p>
                            </div>
                            <table class="vouchers-index-table">
                                <thead>
                                    <tr>
                                        <th class="vouchers-index-table-cumstom_name">製品名</th>
                                        <th class="vouchers-index-table-quantity">数量</th>
                                        <th class="vouchers-index-table-price">単価</th>
                                        <th class="vouchers-index-table-total">合計</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_quantity =   0;
                                        $total_price    =   0;
                                    @endphp
                                    @foreach ($grouped_orders as $order)
                                        <tr>
                                            <td class="vouchers-index-table-cumstom_name">{{ data_get($order, "custom_name") }}</td>
                                            <td class="vouchers-index-table-quantity">{{ number_format((float) str_replace(",","",data_get($order, "quantity"))) }}</td>
                                            <td class="vouchers-index-table-price">{{ number_format((float) str_replace(",","",data_get($order, "price")),2) }}</td>
                                            <td class="vouchers-index-table-total">{{ number_format((float) str_replace(",","",data_get($order, "total"))) }}</td>
                                            <td>
                                                <input type="hidden" name="status.voucher">
                                                <input type="checkbox" name="status.voucher">
                                            </td>
                                        </tr>
                                        @php
                                            $total_quantity +=  (float) str_replace(",","",data_get($order, "quantity"));
                                            $total_price    +=  (float) str_replace(",","",data_get($order, "total"));
                                        @endphp
                                    @endforeach
                                    @for ($i = count($grouped_orders); $i < 8; $i++)
                                        <tr>
                                            <td colspan="5"></td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td class="vouchers-index-table-price">{{ number_format($total_quantity) }}</td>
                                        <td class="vouchers-index-table-total">{{ number_format($total_price) }}</td>
                                        <td><input type="checkbox" name="check-all"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </li>

                @endforeach
            </ul>
        </section>
    </x-slot>
    <x-slot name="footer">
    </x-slot>
    <x-slot name="script">
        <script>
            function is_changed(node) {
                const class_name        =   "voucher-index-table-input-changed";
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
                    input.classList.remove("voucher-index-table-input-changed");
                });
            }

            function change_voucher_mode(){
                const vouchers  =   document.querySelectorAll(".voucher-index-table-voucher");
                vouchers.forEach(voucher => voucher.classList.toggle("hidden"));
            }

            function prepare_submit(node){
                const inputs   =   node.querySelectorAll("input:not([name='_token'])");
                inputs.forEach(input=>input.disabled = input.classList.contains("voucher-index-table-input-changed") == false);
            }

            narrow_down_orders(document.querySelector("form[name=voucher-index-query]"));
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
                    const tbody = document.querySelector('table#voucher-index-table > tbody');
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

                    document.querySelector("#voucher-index-total-table-quantity").innerHTML   = total_quantity.toLocaleString();
                    document.querySelector("#voucher-index-total-table-sales").innerHTML      = total_sales.toLocaleString();

                // // ⑥ 並べ替え（ソート）の実行
                //     rows.sort((a,b)=>{
                //         let val_a   =   a.dataset[order_by] || "";
                //         let val_b   =   b.dataset[order_by] || "";
                //         return val_a.localeCompare(val_b, "ja");
                //     });

                //     rows.forEach(row => tbody.appendChild(row));
            }
        </script>
        {{-- <x-voucher.script-hiraganas function_name="narrow_down_companies(node)" table_id="voucher-index-table" /> --}}
    </x-slot>
    <x-slot name="hidden"></x-slot>
</x-body.frame.admin>
