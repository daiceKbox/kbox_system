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

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('KBox System');

        // ① サービスアカウント（JSONファイル）を使う場合
        $credentialsPath = storage_path('app/google/credentials.json');
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
     * 指定したスプレッドシートの範囲の値を取得する
     *
     * @param string $spreadsheetId スプレッドシートID（URLの中のランダムな文字列）
     * @param string $range 取得範囲（例: 'シート1!A1:D10'）
     * @return array
     */
    public function getSheetData(string $spreadsheetId, string $range): array
    {
        try {
            $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
            return $response->getValues() ?? [];
        } catch (Exception $e) {
            // エラーログ出力や例外処理
            Log::error('Google Sheet Error: ' . $e->getMessage());
            return [];
        }
    }
}
