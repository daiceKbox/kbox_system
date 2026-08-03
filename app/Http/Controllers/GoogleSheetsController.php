<?php

namespace App\Http\Controllers;

use App\Libraries\GoogleSheetService;
use Exception;
use Illuminate\Http\Request;

class GoogleSheetsController extends Controller
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
        $companies      =   $this->sheet_service->getSheetData("kbox_company", "companies");
        $data           =   [
            "companies" =>  $companies["data"],
        ];
        return response()->json($data, 200);
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
    public function show(string $code)
    {
        $conditions     =   [
            "code"      =>  $code,
        ];
        $company        =   $this->sheet_service->get_first("kbox_company", "companies", $conditions);
        $data           =   [
            "company"   =>  $company,
        ];
        return response()->json($data, 200);
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
