<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('permission.table_names.permissions'), function (Blueprint $table) {
            $table->string('group')->nullable()->after('guard_name');
            $table->string('title')->nullable()->after('group');
            $table->string('method')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('permission.table_names.permissions'), function (Blueprint $table) {
            $table->dropColumn(['group', 'title', 'method']);
        });
    }
};
