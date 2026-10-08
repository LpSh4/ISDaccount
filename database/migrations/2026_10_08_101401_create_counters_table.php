<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->timestamp('reset_at');
            $table->timestamps();
        });
        DB::table('counters')->insert([
            ['key' => 'no_feeding', 'reset_at' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'no_incidents', 'reset_at' => now(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
