<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('userr', function (Blueprint $table) {
            if (!Schema::hasColumn('userr','status')) {
                $table->enum('status',['pending','approved','rejected'])->default('pending')->after('role');
            }
            if (!Schema::hasColumn('userr','approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('userr','approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
                // opsional relasi
                // $table->foreign('approved_by')->references('id_user')->on('userr')->nullOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('userr', function (Blueprint $table) {
            if (Schema::hasColumn('userr','approved_by')) $table->dropColumn('approved_by');
            if (Schema::hasColumn('userr','approved_at')) $table->dropColumn('approved_at');
            if (Schema::hasColumn('userr','status'))      $table->dropColumn('status');
        });
    }
};
