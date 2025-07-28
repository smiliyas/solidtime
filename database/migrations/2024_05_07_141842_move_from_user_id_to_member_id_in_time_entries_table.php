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
            $table->foreignUuid('member_id')
                ->nullable()
                ->constrained('organization_user')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
        DB::statement('
            UPDATE time_entries
            JOIN organization_user ON time_entries.organization_id = organization_user.organization_id
                AND time_entries.user_id = organization_user.user_id
            SET time_entries.member_id = organization_user.id
        ');
        Schema::table('time_entries', function (Blueprint $table): void {
            $table->uuid('member_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table): void {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
        });
    }
};
