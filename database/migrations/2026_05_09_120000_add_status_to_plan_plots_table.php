<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_plots', function (Blueprint $table) {
            $table->string('status', 32)->default('unfinished')->after('plot_number');
        });
    }

    public function down(): void
    {
        Schema::table('plan_plots', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
