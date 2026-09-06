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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'service_fee')) {
                    $table->decimal('service_fee', 10, 2)->default(0)->after('status');
                }
                if (!Schema::hasColumn('payments', 'vat_amount')) {
                    $table->decimal('vat_amount', 10, 2)->default(0)->after('service_fee');
                }
                if (!Schema::hasColumn('payments', 'total_paid')) {
                    $table->decimal('total_paid', 10, 2)->nullable()->after('vat_amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('payments', 'service_fee')) {
                    $columns[] = 'service_fee';
                }
                if (Schema::hasColumn('payments', 'vat_amount')) {
                    $columns[] = 'vat_amount';
                }
                if (Schema::hasColumn('payments', 'total_paid')) {
                    $columns[] = 'total_paid';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
