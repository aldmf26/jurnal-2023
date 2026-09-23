<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku_campur', function (Blueprint $table) {
            $table->index(['gabung', 'approve', 'gudang'], 'idx_bc_gabung_approve_gudang');
            $table->index(['gabung', 'approve'], 'idx_bc_gabung_approve');
        });
        Schema::table('buku_campur_approve', function (Blueprint $table) {
            $table->index('gudang', 'idx_bca_gudang');
            $table->index(['gudang', 'ket2'], 'idx_bca_gudang_ket2');
        });
    }

    public function down(): void
    {
        Schema::table('buku_campur', function (Blueprint $table) {
            $table->dropIndex('idx_bc_gabung_approve_gudang');
            $table->dropIndex('idx_bc_gabung_approve');
        });
        Schema::table('buku_campur_approve', function (Blueprint $table) {
            $table->dropIndex('idx_bca_gudang');
            $table->dropIndex('idx_bca_gudang_ket2');
        });
    }
};
