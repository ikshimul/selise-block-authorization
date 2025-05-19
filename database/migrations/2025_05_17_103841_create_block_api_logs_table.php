<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('block_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->text('payload');
            $table->longText('response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_api_logs');
    }
};
