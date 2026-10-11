<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table): void {
            $table->index('created_at', 'maintenance_requests_created_at_index');
            $table->index('updated_at', 'maintenance_requests_updated_at_index');
            $table->index(
                ['maintenance_personnel_id', 'status'],
                'maintenance_requests_personnel_status_index',
            );
        });

        Schema::table('chat_messages', function (Blueprint $table): void {
            $table->index(
                ['maintenance_request_id', 'created_at'],
                'chat_messages_request_created_at_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table): void {
            $table->dropIndex('maintenance_requests_created_at_index');
            $table->dropIndex('maintenance_requests_updated_at_index');
            $table->dropIndex('maintenance_requests_personnel_status_index');
        });

        Schema::table('chat_messages', function (Blueprint $table): void {
            $table->dropIndex('chat_messages_request_created_at_index');
        });
    }
};
