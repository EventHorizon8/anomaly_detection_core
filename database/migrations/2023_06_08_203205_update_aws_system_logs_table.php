<?php

declare(strict_types=1);

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
        Schema::table('aws_system_logs', function (Blueprint $table) {
            $table->bigInteger('return_value')->nullable()->change()->comment('data from logs');
            $table->text('stack_address')->nullable()->change()->comment('data from logs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
