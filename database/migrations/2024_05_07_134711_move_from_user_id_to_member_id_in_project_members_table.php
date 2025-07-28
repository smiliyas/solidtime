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
        Schema::table('project_members', function (Blueprint $table): void {
            $table->foreignUuid('member_id')
                ->nullable()
                ->constrained('organization_user')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
        DB::statement('
            UPDATE project_members
            INNER JOIN projects ON projects.id = project_members.project_id
            INNER JOIN organization_user ON organization_user.organization_id = projects.organization_id
                AND organization_user.user_id = project_members.user_id
            SET project_members.member_id = organization_user.id
        ');
        Schema::table('project_members', function (Blueprint $table): void {
            $table->uuid('member_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_members', function (Blueprint $table): void {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
        });
    }
};
