<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('level_id')->nullable()->after('resource_id')->constrained()->onDelete('set null');
            $table->index('level_id');
        });

        // Set default level for existing events
        $defaultLevel = DB::table('levels')->first();
        if ($defaultLevel) {
            DB::table('events')->update(['level_id' => $defaultLevel->id]);
        }
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
            $table->dropColumn('level_id');
        });
    }
};