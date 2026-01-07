<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->integer('fine')->default(0);
            $table->boolean('is_paid')->default(false);
            $table->string('virtual_account')->nullable();
            $table->date('returned_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn(['fine','is_paid','virtual_account','returned_at']);
        });
    }
};
