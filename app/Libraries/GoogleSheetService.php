<?php

namespace App\Libraries;

use Google\Client;
use Google\Service\Sheets;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected Client $client;
    protected Sheets $service;

    protected array $spreadsheets   =   [
        "kbox_company"  =>  "1E9dXu3jT_nTl5XZJoVso681YJJIIzocwdDG0KcNbbW0",
        "kbox_product"  =>  "1iS4afNjUZ0oJyxDOUk_AV0oemp02ziB23eRPlTgKFhQ",
        "kbox_order"    =>  "1NHVylnmXweYPWdIy1m8Id0P7aR3f_ghgOvi-tRzgDpQ",
    ];

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('KBox System');

        // ① サービスアカウント（JSONファイル）を使う場合
        $credentialsPath = storage_path('app/private/kbox-system-google-credentials.json');
        if (file_exists($credentialsPath)) {
            $this->client->setAuthConfig($credentialsPath);
            $this->client->addScope(Sheets::SPREADSHEETS_READONLY);
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
            $spreadsheetId  = $this->get_shpreadsheet_id($spreadsheet_key_or_id);
            $response       = $this->service->spreadsheets_values->get($spreadsheetId, $range);
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
        // 全データを取得
        $response = $this->get_all($spreadsheet_key_or_id, $range);

        if ($response['status'] !== 'success' || empty($response['data'])) {
            return null;
        }

        // Laravel Collectionに変換して絞り込み
        return collect($response['data'])->first(function ($item) use ($conditions) {
            foreach ($conditions as $header => $target_value) {
                // ネストされたキー（"structure.type" や "contact.tel" など）にも対応するため data_get を利用
                $value = data_get($item, $header);
                // 比較（型を問わず一致するか、文字列として比較）
                if ((string)$value !== (string)$target_value) {
                    return false;
                }
            }
            return true;
        });
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
        // 全データを取得
        $response = $this->get_all($spreadsheet_key_or_id, $range);

        if ($response['status'] !== 'success' || empty($response['data'])) {
            return [];
        }

        // Laravel Collectionに変換して条件に一致するものをすべてフィルタリング
        return collect($response['data'])->filter(function ($item) use ($conditions) {
            foreach ($conditions as $header => $target_value) {
                // ネストされたキーにも対応
                $value = data_get($item, $header);
                // 比較（文字列として比較）
                if ((string)$value !== (string)$target_value) {
                    return false;
                }
            }
            return true;
        })->values()->all(); // インデックスを0から再振り分けして純粋な配列として返す
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
}
