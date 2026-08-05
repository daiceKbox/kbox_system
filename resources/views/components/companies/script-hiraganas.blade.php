@props([
    'function_name' =>  "narrow_down_companies(node)",
    "table_id"      =>  "companies-index-table",
])

<script>
    /**
     * 五十音ボタン押下時の企業リスト絞り込み
     * @param {HTMLButtonElement} node クリックされた button 要素
     */
    function {{ $function_name }}{
        const tbody =   document.querySelector("#{{ $table_id }} > tbody");
        const rows  =   document.querySelectorAll("#{{ $table_id }} > tbody > tr");

        const value =   node.value;
        const tag   =   node.tagName;
        console.log(tag);
        if(tag  == "BUTTON"){
            if(value == "reset") {
                rows.forEach(row => row.style.display = "");
            } else {
                rows.forEach(row=>{
                    const kana  =   normalize_kana(katakana_to_hiragana(row.dataset.kana));
                    if(kana && kana[0] == value){
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            }
        }
        if(tag == "INPUT"){
            if(value == "reset") {
                rows.forEach(row => row.style.display = "");
            } else {
                rows.forEach(row=>{
                    const katakana  =   row.dataset.kana || "";
                    const hiragana  =   katakana_to_hiragana(katakana);
                    const name      =   row.dataset.name || "";
                    if(name.includes(value) || katakana.includes(value) || hiragana.includes(value)){
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });

            }
        }
    }

    function katakana_to_hiragana(str) {
        if(! str) return "";
        const hiragana  =   str.replace(/[\u30a1-\u30f6]/g, match => {
            return String.fromCharCode(match.charCodeAt(0) - 0x60);
        });
        return hiragana;
    }

    function hiragana_to_katakana(str) {
        if (!str) return "";
        const katakana = str.replace(/[\u3041-\u3096]/g, match => {
            return String.fromCharCode(match.charCodeAt(0) + 0x60);
        });
        return katakana;
    }
    /**
     * 濁音・半濁音・小文字の清音化マップ
     */
    function normalize_kana(str) {
        const map = {
            'が':'か', 'ぎ':'き', 'ぐ':'く', 'げ':'け', 'ご':'こ',
            'ざ':'さ', 'じ':'し', 'ず':'す', 'ぜ':'せ', 'ぞ':'そ',
            'だ':'た', 'ぢ':'ち', 'づ':'つ', 'で':'て', 'ど':'と',
            'ば':'は', 'び':'ひ', 'ぶ':'ふ', 'べ':'へ', 'ぼ':'ほ',
            'ぱ':'は', 'ぴ':'ひ', 'ぷ':'ふ', 'ぺ':'へ', 'ぽ':'ほ',
            'ぁ':'あ', 'ぃ':'い', 'ぅ':'う', 'ぇ':'え', 'ぉ':'お',
            'っ':'つ', 'ゃ':'や', 'ゅ':'ゆ', 'ょ':'よ', 'ゎ':'わ'
        };
        return str.replace(/[が-ぽぁ-ゎ]/g, match => map[match] || match)
    }
</script>
