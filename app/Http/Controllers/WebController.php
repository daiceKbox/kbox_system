<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use function PHPSTORM_META\map;

class WebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user   =   $request->user();
        $menus  =   [
            ["companies","得意先一覧","会社情報の確認・受注の登録・過去の注文履歴の確認"],
            ["orders","受注一覧","受注情報・数量・製造状況の確認・変更"],
            ["users","ユーザー一覧","ユーザー情報の確認・権限の確認・追加登録"],
        ];
        $menus  =   collect($menus)->map(fn($menu)=>[
            "name"          =>  $menu[0],
            "title"         =>  $menu[1],
            "description"   =>  $menu[2],
        ])->all();

        $data   =   [
            "user"  =>  $user,
            "menus" =>  $menus,
        ];
        return view($user->role ?? "user" ,$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
