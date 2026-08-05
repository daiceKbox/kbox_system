<?php

namespace Database\Seeders;

use App\Libraries\GoogleSheetService;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\UserCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(GoogleSheetService $sheet_service): void
    {
        $users  =   $sheet_service->get_all("kbox_user", "users");

        foreach((data_get($users, "data",[])) as $user){
            if(empty($user["email"])) continue;
            User::updateOrCreate([
                "email" =>  $user["email"],
            ],[
                'name'              => $user["name"],
                'email_verified_at' => !empty($user['email_verified_at'])   ?   $user['email_verified_at'] : null,
                'password'          => Hash::make($user['password']         ??  'password123'),
                'remember_token'    => $user['remember_token']              ??  null,
                'role'              => $user['role']                        ??  'user',
                'birthday'          => !empty($user['birthday'])            ?   $user['birthday'] : null,
                'last_name'         => $user['last_name']                   ??  null,
                'first_name'        => $user['first_name']                  ??  null,
                'last_kana'         => $user['last_kana']                   ??  null,
                'first_kana'        => $user['first_kana']                  ??  null,
                'status'            => $user['status']                      ??  'active',
            ]);
        }
        echo "  [seed] users completed\n";

        $companies      =   data_get($sheet_service->get_all("kbox_company", "companies"),"data",[]);
        $user_companies =   data_get($sheet_service->get_all("kbox_user", "user_companies"),"data",[]);
        $company_keys   =   collect($companies)->keyBy(function ($company) {
            return data_get($company, "code")."_".data_get($company,"key");
        });
        // dd($user_companies);
        $emails         =   collect($user_companies)->pluck("email")->filter()->unique();
        $users          =   User::whereIn("email", $emails,"and",false)->pluck("id","email");

        foreach($user_companies as $user_company){
            $email  =   data_get($user_company,"email");
            if(!isset($users[$email])) continue;
            $company_code   =   data_get($user_company,"company_code");
            $company_key    =   data_get($user_company,"company_key");
            $key            =   $company_code."_".$company_key;
            if($company_keys->has($key)){
                UserCompany::updateOrCreate([
                    "user_id"       =>  $users[$email],
                    "company_code"  =>  $company_code,
                    "company_key"   =>  $company_key,
                ],[
                    "role"          =>  data_get($user_company,"role","user"),
                    "status"        =>  data_get($user_company,"status","active"),
                ]);
            }
        }
        echo "  [seed] user_companies completed\n";
    }
}
