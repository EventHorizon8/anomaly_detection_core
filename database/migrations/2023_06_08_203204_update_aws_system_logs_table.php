<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `aws_system_logs` MODIFY `timestamp` TIMESTAMP(6)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
