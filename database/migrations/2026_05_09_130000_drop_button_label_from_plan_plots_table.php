<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_plots', function (Blueprint $table) {
            $table->dropColumn('button_label');
        });
    }

    public function down(): void
    {
        Schema::table('plan_plots', function (Blueprint $table) {
            $table->string('button_label')->nullable()->after('status');
        });
    }
};
