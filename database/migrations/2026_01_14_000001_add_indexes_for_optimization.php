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
        // Índice compuesto para subscription_reports - mejora la query de rango de fechas
        Schema::table('subscription_reports', function (Blueprint $table) {
            $table->index(['created_at', 'subscription_id'], 'idx_subscription_reports_created_subscription');
        });

        // Índices para las tablas de reportes - mejora los JOINs
        Schema::table('report_loans', function (Blueprint $table) {
            $table->index('subscription_report_id', 'idx_report_loans_subscription_report');
        });

        Schema::table('report_other_debts', function (Blueprint $table) {
            $table->index('subscription_report_id', 'idx_report_other_debts_subscription_report');
        });

        Schema::table('report_credit_cards', function (Blueprint $table) {
            $table->index('subscription_report_id', 'idx_report_credit_cards_subscription_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_reports', function (Blueprint $table) {
            $table->dropIndex('idx_subscription_reports_created_subscription');
        });

        Schema::table('report_loans', function (Blueprint $table) {
            $table->dropIndex('idx_report_loans_subscription_report');
        });

        Schema::table('report_other_debts', function (Blueprint $table) {
            $table->dropIndex('idx_report_other_debts_subscription_report');
        });

        Schema::table('report_credit_cards', function (Blueprint $table) {
            $table->dropIndex('idx_report_credit_cards_subscription_report');
        });
    }
};
