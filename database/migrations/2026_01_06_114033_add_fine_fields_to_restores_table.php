<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('returns', function (Blueprint $table) {
            if (!Schema::hasColumn('returns', 'fine')) {
                $table->integer('fine')->default(0);
            }

            if (!Schema::hasColumn('returns', 'is_paid')) {
                $table->boolean('is_paid')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('returns', function (Blueprint $table) {
            if (Schema::hasColumn('returns', 'fine')) {
                $table->dropColumn('fine');
            }

            if (Schema::hasColumn('returns', 'is_paid')) {
                $table->dropColumn('is_paid');
            }
        });
    }
};
