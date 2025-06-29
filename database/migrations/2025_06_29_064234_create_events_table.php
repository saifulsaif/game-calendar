// database/migrations/2024_01_01_000002_create_events_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->foreignId('resource_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_scheduled')->default(false);
            $table->string('field')->nullable();
            $table->string('referee')->nullable();
            $table->text('notes')->nullable();
            $table->string('duration')->default('01:30:00');
            $table->timestamps();
            
            $table->index(['is_scheduled', 'start']);
            $table->index('resource_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};