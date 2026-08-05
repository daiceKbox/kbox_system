<x-body.frame.admin>
    <x-slot name="name">companies</x-slot>
    <x-slot name="title">得意先</x-slot>
    <x-slot name="head"></x-slot>
    <x-slot name="header"></x-slot>
    <x-slot name="page_transition_list"></x-slot>
    <x-slot name="main">
        <section id="companies-index-query">
            <h3>絞り込み</h3>
            <x-companies.table-hiraganas id="companies-index-query-table" function_name="narrow_down_companies(this);"/>
        </section>
        <section id="companies-index">
            <h3>会社一覧</h3>
            <table id="companies-index-table">
                <thead>
                    <tr>
                        <th class="companies-index-table-name">会社名</th>
                        <th class="companies-index-table-address">会社情報</th>
                        <th class="companies-index-table-action">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (($companies ?? []) as $company)
                        <tr data-row_index="{{ data_get($company,"row_index") }}" data-name="{{ data_get($company,"company_name") }}" data-kana="{{ data_get($company,"kana") }}">
                            <td class="companies-index-table-name">
                                <dl>
                                    <dd class="companies-index-table-name-company_name">{{ data_get($company, "company_name") }}</dd>
                                </dl>
                                <dl>
                                    <dd class="companies-index-table-name-kana">{{ data_get($company, "kana") }}</dd>
                                </dl>
                            </td>
                            <td class="companies-index-table-address">
                                <dl>
                                    <dd class="companies-index-table-address-address">{!! nl2br(e(data_get($company, "address.address"))) !!}</dd>
                                </dl>
                                <dl>
                                    <dd class="companies-index-table-address-tel">{{ data_get($company, "contact.tel") }}</dd>
                                    <dd class="companies-index-table-address-fax">{{ data_get($company, "contact.fax") }}</dd>
                                </dl>
                            </td>
                            <td class="companies-index-table-action">
                                <button onclick="location.href='{{ url('/companies/'.data_get($company, 'code')) }}'">詳細</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </x-slot>
    <x-slot name="footer">
    </x-slot>
    <x-slot name="script">
        <x-companies.script-hiraganas function_name="narrow_down_companies(node)" table_id="companies-index-table" />
    </x-slot>
    <x-slot name="hidden"></x-slot>
</x-body.frame.admin>
