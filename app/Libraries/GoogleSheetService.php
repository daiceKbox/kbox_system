<?php

namespace App\Libraries;

use Google\Client;
use Google\Service\Sheets;
use Exception;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected Client $client;
    protected Sheets $service;

    protected array $spreadsheets   =   [
        "kbox_company"  =>  "1E9dXu3jT_nTl5XZJoVso681YJJIIzocwdDG0KcNbbW0",
        "kbox_product"  =>  "1iS4afNjUZ0oJyxDOUk_AV0oemp02ziB23eRPlTgKFhQ",
        "kbox_order"    =>  "1NHVylnmXweYPWdIy1m8Id0P7aR3f_ghgOvi-tRzgDpQ",
        "kbox_user"     =>  "1wL0YX7zcyUUAiSNTpI2E7Fl5bVBNiGoeYkYEHPxFUyk",
        "kbox_day"      =>  "1vcriVm-Uf9bbNWml2NjcamO0u5RXJp6S4_5h_e3se3I",
    ];

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('KBox System');

        // ① サービスアカウント（JSONファイル）を使う場合
        $credentialsPath = storage_path('app/private/kbox-system-google-credentials.json');
        if (file_exists($credentialsPath)) {
            $this->client->setAuthConfig($credentialsPath);
            $this->client->addScope(Sheets::SPREADSHEETS);
        }
        // ② パブリック公開されているシートを API キーで取得する場合
        elseif (env('GOOGLE_SHEETS_API_KEY')) {
            $this->client->setDeveloperKey(env('GOOGLE_SHEETS_API_KEY'));
        } else {
            throw new Exception('Google API 認証設定が見つかりません。');
        }

        $this->service = new Sheets($this->client);
    }


    /**
     * キー名（またはID直接）からスプレッドシートIDを解決する
     */
    protected function get_shpreadsheet_id(string $key_or_id): string
    {
        // 定義されたキー（例: 'kbox_company'）なら対応するIDを返し、
        // 登録されていない文字列なら「直接IDが渡された」とみなす
        return $this->spreadsheets[$key_or_id] ?? $key_or_id;
    }

    /**
     * 指定したスプレッドシートの範囲の値を取得する
     *
     * @param string $spreadsheet_key_or_id キー名（例: 'kbox_company'）または スプレッドシートID
     * @param string $range 取得範囲（例: 'シート1!A1:D10' または 'シート1'）
     * @return array
     */
    public function get_all(string $spreadsheet_key_or_id, string $range): array
    {
        try {
            $spreadsheet_id  = $this->get_shpreadsheet_id($spreadsheet_key_or_id);
            $response       = $this->service->spreadsheets_values->get($spreadsheet_id, $range);
            $raw_values     = $response->getValues() ?? [];
            $formatted_data = $this->format_rows_with_header($raw_values);

            // 成功時のレスポンスを返す
            return [
                'status'    => 'success',
                'row_count' => count($formatted_data),
                "message"   =>  "データ取得成功",
                'data'      => $formatted_data,
            ];
        } catch (Exception $e) {
            Log::error('Google Sheet Error: ' . $e->getMessage());
            // エラー発生時のレスポンスを返す
            return [
                'status'    => 'error',
                "row_count" =>  null,
                'message'   => $e->getMessage(),
                'data'      => [],
            ];
        }
    }

    /**
     * 条件（配列）に一致する最初の1件を取得する
     *
     * @param string $spreadsheet_key_or_id
     * @param string $range
     * @param array $conditions 検索条件 例: ['code' => '0101'] や ['structure.type' => '株式会社']
     * @return array|null 見つかった場合は該当行の連想配列、見つからない場合は null
     */
    public function get_first(string $spreadsheet_key_or_id, string $range, array $conditions): ?array
    {
        // API呼び出し（get_allを通さず生の2次元配列を取得）
            $spreadsheet_id = $this->get_shpreadsheet_id($spreadsheet_key_or_id);
            $response      = $this->service->spreadsheets_values->get($spreadsheet_id, $range);
            $raw_values    = $response->getValues() ?? [];

            if (empty($raw_values) || count($raw_values) < 2) {
                return null;
            }

        // 1行目をヘッダーとして解析
            $raw_headers = array_shift($raw_values);
            $headers     = [];
            foreach ($raw_headers as $col_index => $header) {
                $cleaned                = preg_replace('/\r\n|\r|\n/', '.', trim((string)$header));
                $headers[$col_index]    = !empty($cleaned) ? $cleaned : 'col_' . $col_index;
            }

        // 条件のキーを列インデックス（0, 1, 2...）に変換
            $condition_indexes = [];
            foreach ($conditions as $key => $target_value) {
                $col_index = array_search($key, $headers, true);
                if ($col_index !== false) {
                    $condition_indexes[$col_index] = (string)$target_value;
                } else {
                    return null;
                }
            }
        // 生の配列のまま判定（見つかった最初の1件だけを連想配列化）
            foreach ($raw_values as $index => $row) {
                $match = true;
                foreach ($condition_indexes as $col_index => $target_value) {
                    $value = $row[$col_index] ?? '';
                    if ((string)$value !== $target_value) {
                        $match = false;
                        break; // 条件不一致なら即次の行へ
                    }
                }

                if ($match) {
                    // 一致した1件だけを連想配列化して即リターン
                    // スプレッドシート上の絶対的な行番号（ヘッダーが1行目なのでデータは2行目から開始）
                        $row_index = $index + 2;

                    // ヘッダーと行の値をマッピングして連想配列（オブジェクト）を作成
                    // ★ 絶対にズレないスプレッドシート上の行番号を付与
                        $result = [
                            "row_index" =>  $row_index,
                        ];

                        foreach ($headers as $col_index => $header) {
                            // ヘッダー名が空の場合は列インデックス等をフォールバック
                            $key    =   !empty($header)     ? trim($header) : 'col_' . $col_index;
                            $value  =   $row[$col_index]    ??  null;
                            // ★ Laravelの data_set を使うことで "structure.type" を連想配列の階層構造（['structure']['type']）に自動変換
                            data_set($result, $key, $value);
                        }
                        return $result;
                }
            }
            return null;
    }

    /**
     * 条件（配列）に一致するすべてのデータを取得する
     *
     * @param string $spreadsheet_key_or_id
     * @param string $range
     * @param array $conditions 検索条件 例: ['structure.type' => '株式会社']
     * @return array 一致したデータの配列（見つからない場合は空配列 []）
     */
    public function get_where(string $spreadsheet_key_or_id, string $range, array $conditions): array
    {
        // API呼び出し
        $spreadsheet_id = $this->get_shpreadsheet_id($spreadsheet_key_or_id);
        $response      = $this->service->spreadsheets_values->get($spreadsheet_id, $range);
        $raw_values    = $response->getValues() ?? [];

        if (empty($raw_values) || count($raw_values) < 2) {
            return [];
        }

        // 1行目をヘッダーとして解析・整形
        $raw_headers = array_shift($raw_values);
        $headers     = [];
        foreach ($raw_headers as $col_index => $header) {
            $cleaned             = preg_replace('/\r\n|\r|\n/', '.', trim((string)$header));
            $headers[$col_index] = !empty($cleaned) ? $cleaned : 'col_' . $col_index;
        }

        // 条件のキーを列インデックスに変換
        $condition_indexes = [];
        foreach ($conditions as $key => $target_value) {
            $col_index = array_search($key, $headers, true);
            if ($col_index !== false) {
                $condition_indexes[$col_index] = (string)$target_value;
            } else {
                // 存在しないキー指定時はヒットし得ないため即空配列を返す
                return [];
            }
        }

        $results = [];

        // 生の配列のまま判定（一致した行だけを連想配列化）
        foreach ($raw_values as $index => $row) {
            $match = true;
            foreach ($condition_indexes as $col_index => $target_value) {
                $value = $row[$col_index] ?? '';
                if ((string)$value !== $target_value) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $row_index = $index + 2;
                $result    = ['row_index' => $row_index];

                foreach ($headers as $col_index => $key) {
                    $value = $row[$col_index] ?? null;
                    data_set($result, $key, $value);
                }
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * 生の行列データ（2次元配列）をヘッダーキーの連想配列リストに整形する
     *
     * @param array $raw_values 例: [ ['code', 'name'], ['0001', '北角紙器'] ]
     * @return array
     */
    private function format_rows_with_header(array $raw_values): array
    {
        $result = [];

        if (empty($raw_values)) {
            return $result;
        }

        // 1. 1行目をヘッダー（キー）として抽出
        $headers        = array_shift($raw_values); // 1行目を取り出す
        $headers        = array_map(function ($header) {
            $cleaned = preg_replace('/\r\n|\r|\n/', '.', trim((string)$header));
            return $cleaned;
        }, $headers);
        $header_count   = count($headers);


        // 2. 2行目以降（データ行）を処理
        // 1行目を array_shift しているため、$index = 0 が スプレッドシートの 2行目 (row_index = 2) に対応します
        foreach ($raw_values as $index => $row) {

            // スプレッドシート上の絶対的な行番号（ヘッダーが1行目なのでデータは2行目から開始）
            $row_index = $index + 2;

            // スプレッドシートの末尾の空セル対策（ヘッダーの数と行の列数を揃える）
            $row = array_pad($row, $header_count, null);

            // ヘッダーと行の値をマッピングして連想配列（オブジェクト）を作成
            $mapped_row = [];

            // ★ 絶対にズレないスプレッドシート上の行番号を付与
            $mapped_row['row_index'] = $row_index;

            foreach ($headers as $col_index => $header) {
                // ヘッダー名が空の場合は列インデックス等をフォールバック
                $key    =   !empty($header)     ? trim($header) : 'col_' . $col_index;
                $value  =   $row[$col_index]    ??  null;
                // ★ Laravelの data_set を使うことで "structure.type" を連想配列の階層構造（['structure']['type']）に自動変換
                data_set($mapped_row, $key, $value);
            }
            $result[] = $mapped_row;
        }
        return $result;
    }

    /**
     * データをシート最上行（1行目）のヘッダー順に合わせて並び替え、シートの末尾に追加する
     *
     * @param string $spreadsheet_key_or_id スプレッドシートID
     * @param string $sheet_name シート名（例: "シート1" や "products"）
     * @param array $data 追加するデータ（1件の連想配列、または連想配列の多次元配列）
     * @return \Google\Service\Sheets\AppendValuesResponse
     */
    public function append_rows(string $spreadsheet_key_or_id, string $sheet_name, array $data)
    {
        $spreadsheet_id = $this->get_shpreadsheet_id($spreadsheet_key_or_id);

        // 1. シートの1行目（ヘッダー）のみを取得
        $range      = $sheet_name."!1:1";
        $response   = $this->service->spreadsheets_values->get($spreadsheet_id, $range);
        $headers    = $response->getValues()[0] ?? [];

        if (empty($headers)) {
            throw new \Exception("指定されたシート（{$sheet_name}）の1行目にヘッダーが見つかりません。");
        }
        // 2. 渡された配列のキーを 0 始まりにリセット
        $data = array_values($data);

        // 単一の連想配列（["code" => "0101", ...]) が渡された場合は2次元配列に統一
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        // 3. ヘッダーのキー順に合わせてデータを並び替え（存在しないキーは null を補填）
        $rows_to_append = [];
        foreach ($data as $row_item) {
            $row_array = [];
            foreach ($headers as $header) {
                // 改行除去・トリムして取得時と同じ表現のキーに揃える
                $key    = preg_replace('/\r\n|\r|\n/', '.', trim((string)$header));

                // Laravelの data_get を使うことで "structure.type" 等のドット記法・階層データにも対応
                $value  = data_get($row_item, $key, "");
                // null または 未設定 の場合は空文字に置換（null のままだと JSON 化で連想配列化する原因になる）
                if (is_null($value)) {
                    $value = '';
                } elseif (is_array($value) || is_object($value)) {
                    // 配列やオブジェクトが返ってきた場合は JSON 文字列化
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                $row_array[] = $value;
            }
            // 確実にかぎ括弧［0, 1, 2...］の数値インデックス配列に変換して追加
            $rows_to_append[] = array_values($row_array);
        }

        // 4. Google_Service_Sheets_ValueRange の作成
        $body = new Google_Service_Sheets_ValueRange();

        // 外側の配列も完全な数値インデックスに保証してセット
        $body->setValues(array_values($rows_to_append));

        // 5. シート末尾に追加（USER_ENTERED で入力形式を維持）
        $params = [
            'valueInputOption' => 'USER_ENTERED',
        ];

        return $this->service->spreadsheets_values->append(
            $spreadsheet_id,
            $sheet_name,
            $body,
            $params
        );
    }

/**
     * 条件に一致する行を検索し、指定したヘッダー（列）のセルに値を書き込む
     *
     * @param string $spreadsheet_key_or_id スプレッドシートのIDまたはキー
     * @param string $sheet_name シート名（例: 'Sheet1'）
     * @param array $conditions 検索条件（例: ['id' => 123]）
     * @param string $header 書き換えたい列のヘッダー名（例: 'ステータス'）
     * @param mixed $value 書き込む値
     */
    public function update_cell(string $spreadsheet_key_or_id, string $sheet_name, array $conditions, $header, $value)
    {
        // 1. get_first を使って条件に一致する最初の行を特定する（row_index も一緒に取れる！）
            $row = $this->get_first($spreadsheet_key_or_id, $sheet_name, $conditions);

            if (empty($row)) {
                $cond_str = collect($conditions)->map(fn($v, $k) => "{$k} = {$v}")->implode(', ');
                throw new \Exception("条件（{$cond_str}）に一致する行が見つかりませんでした。");
            }

            $target_row_index   =   $row['row_index'];

        // 2. ヘッダー名から列のアルファベットを特定するために1行目を取得
            $spreadsheet_id = $this->get_shpreadsheet_id($spreadsheet_key_or_id);
            $response       = $this->service->spreadsheets_values->get($spreadsheet_id, "{$sheet_name}!1:1");
            $headers        = $response->getValues()[0] ?? [];

            $header_index   = array_search($header, array_map(fn($h) => preg_replace('/\r\n|\r|\n/', '.', trim((string)$h)), $headers), true);

            if ($header_index === false) {
                throw new \Exception("指定されたヘッダー（{$header}）が見つかりません。");
            }

            $column_letter  =   $this->index_to_column_letter($header_index);
            $target_range   =   "{$sheet_name}!{$column_letter}{$target_row_index}";

        // 3. 書き込む値の整形（1次元やスカラー値を2次元配列に）
            if (!is_array($value)) {
                $value = [[$value]];
            } elseif (isset($value[0]) && !is_array($value[0])) {
                $value = [$value];
            }

            $body = new Google_Service_Sheets_ValueRange();
            $body->setValues($value);

            $params = [
                'valueInputOption' => 'USER_ENTERED',
            ];

            return $this->service->spreadsheets_values->update(
                $spreadsheet_id,
                $target_range,
                $body,
                $params
            );
    }

    /**
     * 列のインデックス（0始まり）を A1形式の列文字（A, B, C, ..., AA, AB...）に変換する
     */
    private function index_to_column_letter(int $index): string
    {
        $letter = '';
        $index++;
        while ($index > 0) {
            $modulo = ($index - 1) % 26;
            $letter = chr(65 + $modulo) . $letter;
            $index  = (int)(($index - $modulo) / 26);
        }
        return $letter;
    }
}
