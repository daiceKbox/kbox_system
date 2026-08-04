<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCompany extends Model
{
        protected $fillable = [
            "user_id",
            "company_code",
            "company_key",
            "role",
            "status",
        ];
}
