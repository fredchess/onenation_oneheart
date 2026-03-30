<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('payment_status')->default(PaymentStatus::PENDING->value)->after('amount');
        });

        DB::table('donations')
            ->where('status', 1)
            ->update(['payment_status' => PaymentStatus::SUCCESS->value]);

        DB::table('donations')
            ->where('status', 0)
            ->update(['payment_status' => PaymentStatus::FAILED->value]);

        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->integer('status')->default(0)->after('amount');
        });

        DB::table('donations')
            ->where('payment_status', PaymentStatus::SUCCESS->value)
            ->update(['status' => 1]);

        DB::table('donations')
            ->whereIn('payment_status', [
                PaymentStatus::PENDING->value,
                PaymentStatus::FAILED->value,
            ])
            ->update(['status' => 0]);

        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
