<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->string('requester_name')->nullable()->after('reference');
            $table->string('requester_email')->nullable()->after('requester_name');
            $table->timestamp('confirmation_email_sent_at')->nullable()->after('status');
            $table->timestamp('resolved_email_sent_at')->nullable()->after('confirmation_email_sent_at');
            $table->index('requester_email');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropIndex(['requester_email']);
            $table->dropColumn([
                'requester_name',
                'requester_email',
                'confirmation_email_sent_at',
                'resolved_email_sent_at',
            ]);
        });
    }
};
