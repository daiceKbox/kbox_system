<?php

namespace App\Http\Middleware;

use App\Libraries\GoogleSheetService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserCompanyRole
{
    protected GoogleSheetService $service;
    public function __construct(GoogleSheetService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // 未ログイン、または role が未設定
        if (! $user) {
            abort(403, 'アクセス権限がありません。');
        }
        // 【特例】システム管理者は全企業アクセス許可とする場合（必要に応じて）
        if ($user->role === 'admin') {
            return $next($request);
        }
        $company_code   =   $request->route('company_code')     //  web.phpで{company_code}としている部分を取得できる
                        ??  $request->input("company_code")     //  postのパラメータの company_code の値
                        ??  $request->query('company_code');    //  getのqeuryの company_code の値
        if (! $company_code) {
            abort(403, '該当する会社コードが見つかりません。');
        }
        $company        =   $this->service->get_first("kbox_company","companies",["code" => $company_code]);
        if (! $company) {
            abort(403, '該当する会社情報がスプレッドシート内に見つかりません。');
        }
        $target_code    =   data_get($company, "code");
        $target_key     =   data_get($company, "key");
        $has_access     =   $user->user_companies->contains(function ($user_company) use ($target_code, $target_key){
            return (string) $user_company->company_code == $target_code && (string) $user_company->company_key == $target_key;
        });
        if (! $has_access) {
            abort(403, 'この会社データへのアクセス権限がありません。');
        }

        return $next($request);
    }
}
