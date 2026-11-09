<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::connection($this->connection)->create('session_interruptions', function (Blueprint $table) {
            $table->string('session_id', 100)->primary();
            $table->string('device_id', 100)->nullable()->index();
            $table->timestamp('interrupted_at')->useCurrent();
            $table->string('reason', 100)->default('heartbeat_timeout');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('session_interruptions');
    }
};
