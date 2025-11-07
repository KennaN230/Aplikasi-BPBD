<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('userr', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])
                      ->default('pending')
                      ->after('role');
            }
            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('users', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable()->after('approved_by');
            }
        });
    }

    public function down(): void {
        Schema::table('userr', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_seen_at')) $table->dropColumn('last_seen_at');
            if (Schema::hasColumn('users', 'approved_by')) $table->dropColumn('approved_by');
            if (Schema::hasColumn('users', 'approved_at')) $table->dropColumn('approved_at');
            if (Schema::hasColumn('users', 'status')) $table->dropColumn('status');
        });
    }
};
