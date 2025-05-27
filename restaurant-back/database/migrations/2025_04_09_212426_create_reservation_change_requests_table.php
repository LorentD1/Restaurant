<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservation_change_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->enum('type', ['edit', 'delete']);
            $table->text('reason')->nullable();
            $table->json('new_data')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_change_requests');
    }
};
