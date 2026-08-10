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
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_mayorista', 10, 2)->nullable()->default(0.00)->after('precio_venta');
            $table->integer('cant_mayorista')->nullable()->default(6)->after('precio_mayorista');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['precio_mayorista', 'cant_mayorista']);
        });
    }
};
