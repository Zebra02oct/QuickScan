<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnsToDrop = array_values(array_filter([
            'is_kelas_only',
            'wali_kelas_id',
            'kelas_id',
        ], fn($column) => Schema::hasColumn('sesi_absensis', $column)));

        if (! empty($columnsToDrop)) {
            Schema::table('sesi_absensis', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    public function down(): void
    {
        $missingColumns = array_values(array_filter([
            'is_kelas_only',
            'wali_kelas_id',
            'kelas_id',
        ], fn($column) => ! Schema::hasColumn('sesi_absensis', $column)));

        if (! empty($missingColumns)) {
            Schema::table('sesi_absensis', function (Blueprint $table) use ($missingColumns) {
                foreach ($missingColumns as $column) {
                    if ($column === 'is_kelas_only') {
                        $table->boolean('is_kelas_only')->default(false)->after('status');
                    } elseif ($column === 'wali_kelas_id') {
                        $table->unsignedBigInteger('wali_kelas_id')->nullable()->after('is_kelas_only');
                    } elseif ($column === 'kelas_id') {
                        $table->unsignedBigInteger('kelas_id')->nullable()->after('wali_kelas_id');
                    }
                }
            });
        }
    }
};
