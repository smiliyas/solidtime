<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_entries', function (Blueprint $table): void {
            $table->foreignUuid('client_id')
                ->nullable()
                ->constrained('clients')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
        DB::statement('
            UPDATE time_entries
            JOIN projects ON time_entries.project_id = projects.id
            JOIN clients ON projects.client_id = clients.id
            SET time_entries.client_id = clients.id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table): void {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }
};
