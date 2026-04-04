<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annex7_contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('tel')->nullable();
            $table->string('mail')->nullable();
            $table->boolean('is_carrier')->default(false);
            $table->string('means_of_transport')->nullable()->comment('Tylko dla przewoźników');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annex7_contractors');
    }
};
