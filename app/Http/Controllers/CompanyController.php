<?php

namespace App\Http\Controllers;

use App\Libraries\GoogleSheetService;
use Exception;
use Illuminate\Http\Request;

class CompanyController extends Controller
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
        $companies      =   $this->sheet_service->get_all("kbox_company", "companies");
        $data           =   [
            "companies" =>  $companies["data"],
        ];
        return view("companies.index", $data);
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
        $conditions_company             =   [
            "code"      =>  $code,
        ];
        $conditions_company_products    =   [
            "company_code"      =>  $code,
            "status"            =>  "active",
        ];
        $conditions_working_date        =   [
            "date"      =>  now()->format("Y/m/d"),
            "key"       =>  "北角紙器株式会社",
        ];
        $company            =   $this->sheet_service->get_first("kbox_company", "companies", $conditions_company);
        $company_products   =   $this->sheet_service->get_where("kbox_company", "company_products", $conditions_company_products);
        $working_date       =   $this->sheet_service->get_first("kbox_day","days", $conditions_working_date);
        $data               =   [
            "company"           =>  $company,
            "company_products"  =>  $company_products,
            "working_date"      =>  $working_date,
        ];
        return view("companies.show", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code)
    {
        $company_code       =   $request->input("company_code", []);
        $company_name       =   $request->input("company_name", null);
        $company_products   =   $request->input("company_products", null);
        $orders             =   [];
        foreach($company_products as $company_code => $products){
            foreach($products as $product_code => $item){
                $product_name   =   data_get($item, "product_name");
                $custom_name    =   data_get($item, "custom_name");
                $price          =   data_get($item, "price");
                $quantity       =   data_get($item, "quantity");
                $date           =   data_get($item, "date");
                if(!empty($quantity)){
                    $orders[]  =   [
                        'company_code'  =>  $company_code,
                        'company_name'  =>  $company_name,
                        'product_code'  =>  $product_code,
                        "product_name"  =>  $product_name,
                        "custom_name"   =>  $custom_name,
                        'price'         =>  $price,
                        'quantity'      =>  $quantity,
                        'date'          =>  $date,
                    ];
                }
            }
        }
        return response()->json($orders, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
