<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->index(['status', 'updated_at'], 'maintenance_requests_status_updated_at_index');
            $table->index(['status', 'created_at'], 'maintenance_requests_status_created_at_index');
        });

        Schema::table('maintenance_personnel', function (Blueprint $table) {
            $table->index(['employment_status', 'availability'], 'maintenance_personnel_status_availability_index');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropIndex('maintenance_requests_status_updated_at_index');
            $table->dropIndex('maintenance_requests_status_created_at_index');
        });

        Schema::table('maintenance_personnel', function (Blueprint $table) {
            $table->dropIndex('maintenance_personnel_status_availability_index');
        });
    }
};
