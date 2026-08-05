<?php

namespace App\Http\Controllers;

use App\Libraries\GoogleSheetService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected GoogleSheetService $sheet_service;

    public function __construct(GoogleSheetService $sheet_service)
    {
        $this->sheet_service    =   $sheet_service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders =   $this->sheet_service->get_all("kbox_order", "orders");
        $data   =   [
            "orders" =>  $orders["data"],
        ];
        return view("orders.index", $data);
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
