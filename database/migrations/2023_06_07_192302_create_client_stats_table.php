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
        Schema::create('client_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->comment('Client id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->decimal('cpu', 9, 2);
            $table->decimal('ram', 9, 2);
            $table->decimal('free_ram', 9, 2);
            $table->decimal('network_io', 9, 2);
            $table->decimal('disk_space', 9, 2);
            $table->decimal('disk_io', 9, 2);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_stats');
    }
};
