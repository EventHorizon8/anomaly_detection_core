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
        Schema::create('aws_system_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->comment('Client id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->timestamp('timestamp')->comment('data from logs');
            $table->unsignedBigInteger('process_id')->comment('data from logs');
            $table->unsignedBigInteger('thread_id')->comment('data from logs');
            $table->unsignedBigInteger('parent_process_id')->comment('data from logs');
            $table->unsignedBigInteger('user_id')->comment('data from logs');
            $table->unsignedBigInteger('mount_namespace')->comment('data from logs');
            $table->string('process_name', 255)->comment('data from logs');
            $table->string('host_name', 255)->comment('data from logs');
            $table->unsignedBigInteger('event_id')->comment('data from logs');
            $table->string('event_name', 255)->comment('data from logs');
            $table->string('stack_address', 255)->nullable()->comment('data from logs');
            $table->unsignedBigInteger('args_num')->nullable()->comment('data from logs');
            $table->unsignedBigInteger('return_value')->nullable()->comment('data from logs');
            $table->text('args')->nullable()->comment('data from logs');
            $table->string('hash', 255);
            $table->decimal('outlier_anomaly_score', 5, 2)->nullable();
            $table->boolean('anomaly_detected')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aws_system_logs');
    }
};
