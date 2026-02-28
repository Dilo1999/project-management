<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('assigned_by_user_id')->constrained()->nullOnDelete();
            $table->text('note')->nullable()->after('description');
            $table->timestamp('done_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['note', 'done_at']);
        });
    }
};
