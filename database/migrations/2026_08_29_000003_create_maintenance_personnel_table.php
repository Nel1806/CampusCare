<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_personnel', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('availability')->default('available');
            $table->string('employment_status')->default('active');
            $table->timestamps();
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreignId('maintenance_personnel_id')->nullable()->after('assigned_to')->constrained('maintenance_personnel')->nullOnDelete();
        });

        foreach (DB::table('users')->where('role', 'maintenance')->get() as $user) {
            $personnelId = DB::table('maintenance_personnel')->insertGetId([
                'name' => $user->name,
                'email' => $user->email,
                'availability' => $user->availability ?? 'available',
                'employment_status' => $user->employment_status ?? 'active',
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);

            DB::table('maintenance_requests')->where('assigned_to', $user->id)->update(['maintenance_personnel_id' => $personnelId]);
        }
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['maintenance_personnel_id']);
            $table->dropColumn('maintenance_personnel_id');
        });

        Schema::dropIfExists('maintenance_personnel');
    }
};
