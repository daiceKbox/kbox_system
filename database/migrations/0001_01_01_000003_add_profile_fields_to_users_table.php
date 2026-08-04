<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('first_kana')->nullable()->after('last_name');
            $table->string('last_kana')->nullable()->after('first_kana');
            $table->date('birthday')->nullable()->after('last_kana');

            // ロール定義（デフォルト値を 'user' に設定）
            $table->string('role')->default('user')->after('birthday');
            $table->string("status")->default("active")->after("role");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'first_kana',
                'last_kana',
                'birthday',
                'role',
            ]);
    });
    }
};
