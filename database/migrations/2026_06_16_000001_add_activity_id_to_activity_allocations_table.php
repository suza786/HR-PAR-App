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
        Schema::table('activity_allocations', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_allocations', 'activity_id')) {
                $table->foreignId('activity_id')
                    ->nullable()
                    ->after('project_id')
                    ->constrained('activities')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_allocations', function (Blueprint $table) {
            if (Schema::hasColumn('activity_allocations', 'activity_id')) {
                $table->dropConstrainedForeignId('activity_id');
            }
        });
    }
};
