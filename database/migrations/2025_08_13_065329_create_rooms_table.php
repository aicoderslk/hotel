<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // e.g. Deluxe King
            $table->string('type');        // Standard/Deluxe/Suite
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('price_cents'); // store cents
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('rooms');
    }
};
