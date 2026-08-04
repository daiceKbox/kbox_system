<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
/**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  カンマ区切りで渡されたロールのリスト
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. ログインしていない、またはユーザーの role カラムが空の場合は403エラー
        if (! $request->user() || ! $request->user()->role) {
            abort(403, 'アクセス権限がありません。');
        }

        // 2. ユーザーのロールが、許可されたロールに含まれているかチェック
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'このページを表示する権限がありません。');
        }

        return $next($request);
    }
}
