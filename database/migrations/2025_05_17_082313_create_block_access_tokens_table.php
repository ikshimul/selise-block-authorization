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
        Schema::create('block_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('scope')->nullable();
            $table->string('token_type');
            $table->text('access_token');
            $table->smallInteger('expires_in');
            $table->string('refresh_token');
            $table->timestamp('expire_at');
            $table->timestamps();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_access_tokens');
    }
};
