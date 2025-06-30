<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7); // Hex color
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default levels
        DB::table('levels')->insert([
            ['name' => 'Level 1', 'color' => '#dc3545', 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Level 2', 'color' => '#ffc107', 'order_index' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Level 3', 'color' => '#28a745', 'order_index' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Level 4', 'color' => '#17a2b8', 'order_index' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('levels');
    }
};