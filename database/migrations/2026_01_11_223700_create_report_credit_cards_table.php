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
        Schema::create('report_credit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_report_id')
                ->constrained('subscription_reports')
                ->onDelete('cascade');
            $table->string('bank', 100);
            $table->string('currency', 3)->default('PEN');
            $table->decimal('line', 12, 2);
            $table->decimal('used', 12, 2);
            // $table->timestamps();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_credit_cards');
    }
};
