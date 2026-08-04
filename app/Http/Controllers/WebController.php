<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use function PHPSTORM_META\map;

class WebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus  =   [
            ["companies","得意先一覧","得意先一覧から、会社情報・受注・過去の注文履歴が確認できます"],
            ["users","ユーザー一覧","ユーザー一覧から、ユーザー情報・権限の確認・追加登録ができます"],
        ];
        $menus  =   collect($menus)->map(fn($menu)=>[
            "name"          =>  $menu[0],
            "title"         =>  $menu[1],
            "description"   =>  $menu[2],
        ])->all();

        $data   =   [
            "menus" =>  $menus,
        ];
        return view("home",$data);
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
