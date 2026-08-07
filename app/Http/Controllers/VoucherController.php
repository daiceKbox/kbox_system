<?php

namespace App\Http\Controllers;

use App\Libraries\GoogleSheetService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    protected GoogleSheetService $sheet_service;

    public function __construct(GoogleSheetService $sheet_service)
    {
        $this->sheet_service    =   $sheet_service;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $deadline   =   $request->query("deadline", now()->format("Y-m-d"));
        $orders     =   $this->sheet_service->get_where("kbox_order","orders",["deadline" => str_replace('-', '/', $deadline)]);
        $orders     =   collect($orders)->sortBy('company_code')->groupBy(function ($order) {
            $company_code   =   data_get($order, 'company_code', '');
            $voucher_type   =   data_get($order, 'voucher.type', ''); // まとめたいキー
            return "{$company_code}_{$voucher_type}";
        });
        $data       =   [
            "orders"    =>  $orders,
            "deadline"  =>  $deadline,
        ];
        // return $orders;
        return view("voucher.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
