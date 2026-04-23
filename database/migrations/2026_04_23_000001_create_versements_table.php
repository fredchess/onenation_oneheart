<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orphanage_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('payment_status')->default('pending');
            $table->json('datas')->nullable();
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versements');
    }
};
