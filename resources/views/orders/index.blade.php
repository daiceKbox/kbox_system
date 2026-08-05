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
        <section id="orders-index">
            <h3>受注一覧</h3>
            <table id="orders-index-table">
                <thead>
                    <tr>
                        <th class="orders-index-table-id">ID</th>
                        <th class="orders-index-table-date">受注日</th>
                        <th class="orders-index-table-company" colspan="2">会社名</th>
                        <th class="orders-index-table-product" colspan="2">製品名</th>
                        <th class="orders-index-table-quantity">数量</th>
                        <th class="orders-index-table-price">単価</th>
                        <th class="orders-index-table-deadline">納期</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (($orders ?? []) as $oreder)
                        <tr
                            class="{{ data_get($oreder,"date") == now()->format("Y/m/d") ? "" : "hidden" }}"
                            data-row_index="{{ data_get($oreder,"row_index") }}"
                            data-date="{{ data_get($oreder,"date") }}"
                            data-company_name="{{ data_get($oreder,"company_name") }}"
                            data-product_name="{{ data_get($oreder,"product_name") }}"
                            data-deadline="{{ data_get($oreder,"deadline") }}"
                        >
                            <td class="orders-index-table-company-id">{{ data_get($oreder, "id") }}</td>
                            <td class="orders-index-table-company-date">{{ data_get($oreder, "date") }}</td>
                            <td class="orders-index-table-company-company_code">{{ data_get($oreder, "company_code") }}</td>
                            <td class="orders-index-table-company-company_name">{{ data_get($oreder, "company_name") }}</td>
                            <td class="orders-index-table-product-product_code">{{ data_get($oreder, "product_code") }}</td>
                            <td class="orders-index-table-product-product_name">{{ data_get($oreder, "product_name") }}</td>
                            <td class="orders-index-table-quantity">{{ data_get($oreder, "quantity") }}</td>
                            <td class="orders-index-table-price">{{ data_get($oreder, "price") }}</td>
                            <td class="orders-index-table-deadline">{{ data_get($oreder, "deadline") }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </x-slot>
    <x-slot name="footer">
    </x-slot>
    <x-slot name="script">
        {{-- <x-orders.script-hiraganas function_name="narrow_down_companies(node)" table_id="orders-index-table" /> --}}
    </x-slot>
    <x-slot name="hidden"></x-slot>
</x-body.frame.admin>
