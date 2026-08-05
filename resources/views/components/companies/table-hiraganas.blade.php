@props([
    "id"            =>  "companies-index-query-table",
    "function_name" =>  "narrow_down_companies(this);"
])

@php
    $hiraganas  =   [
        ["あ","い","う","え","お"],
        ["か","き","く","け","こ"],
        ["さ","し","す","せ","そ"],
        ["た","ち","つ","て","と"],
        ["な","に","ぬ","ね","の"],
        ["は","ひ","ふ","へ","ほ"],
        ["ま","み","む","め","も"],
        ["や",null,"ゆ",null,"よ"],
        ["ら","り","る","れ","ろ"],
        ["わ",null,null,null,"を"],
        ["ん",null,null,null,null],
    ];
@endphp
<table id="{{ $id }}">
    <tbody>
        @for ($row=0; $row<5; $row++)
            <tr>
                @for ($col=0; $col<count($hiraganas); $col++)
                    <td>
                        @if ($hiragana = $hiraganas[$col][$row])
                            <button type="button" onclick="{{ $function_name }}" value="{{ $hiragana }}">{{ $hiragana }}</button>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
        <tr>
            <td colspan="{{ count($hiraganas)-3 }}">
                <input id="companies-index-query-input" type="text" oninput="{{ $function_name }}">
            </td>
            <td colspan="3"><button onclick="{{ $function_name }}" value="reset">リセット</button></td>
        </tr>
    </tbody>
</table>
