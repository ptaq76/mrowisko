<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pickup', 'sale']);
            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->nullOnDelete();
            $table->foreignId('start_client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->nullOnDelete();
            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('drivers')
                  ->nullOnDelete();
            $table->foreignId('tractor_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();
            $table->foreignId('trailer_id')
                  ->nullable()
                  ->constrained('vehicles')
                  ->nullOnDelete();
            $table->foreignId('lieferschein_id')
                  ->nullable()
                  ->constrained('lieferscheins')
                  ->nullOnDelete();
            $table->date('planned_date')->nullable();
            $table->date('plac_date')->nullable();       // kiedy widoczne na placu
            $table->time('planned_time')->nullable();
            $table->text('fractions_note')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', [
                'planned', 'loading', 'loaded', 'weighed', 'delivered', 'closed'
            ])->default('planned');
            $table->boolean('is_archived')->default(false);
            $table->decimal('weight_brutto', 10, 2)->nullable();
            $table->decimal('weight_netto', 10, 2)->nullable();
            $table->decimal('weight_receiver', 8, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('order_quick_buttons', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->enum('type', ['goods', 'notes']);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('order_quick_buttons');
        Schema::dropIfExists('orders');
    }
};
