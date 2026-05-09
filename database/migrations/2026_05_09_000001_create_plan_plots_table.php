<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_plots', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('plot_number')->unique()->comment('Matches SVG clickable plot order (1 = first .st0/.st1 shape)');
            $table->string('button_label')->nullable()->comment('Sidebar button text; default Owner N');
            $table->string('owner_name')->nullable();
            $table->text('details')->nullable()->comment('Shown in View details modal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_plots');
    }
};
