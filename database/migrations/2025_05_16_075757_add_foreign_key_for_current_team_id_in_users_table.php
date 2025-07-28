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
        DB::statement('
            UPDATE users
            LEFT JOIN organizations ON users.current_team_id = organizations.id
            SET users.current_team_id = NULL
            WHERE users.current_team_id IS NOT NULL AND organizations.id IS NULL
        ');
        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('current_team_id', 'organizations_current_organization_id_foreign')
                ->references('id')
                ->on('organizations')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign('organizations_current_organization_id_foreign');
        });
    }
};
