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
        Schema::table('orphanages', function (Blueprint $table) {
            if (Schema::hasColumn('orphanages', 'responsable_id')) {
                $table->dropForeign(['responsable_id']);
                $table->dropUnique(['responsable_id']);
            }

            $table->foreignId('responsable_id')->nullable()->change()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
