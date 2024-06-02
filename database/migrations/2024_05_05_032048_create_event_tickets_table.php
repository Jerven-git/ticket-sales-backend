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
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('ticket_name');
            $table->integer('ticket_quantity');
            $table->integer('ticket_price');
            $table->date('sale_start_date');
            $table->date('sale_start_time');
            $table->date('sale_end_date');
            $table->date('sale_end_time');
            $table->boolean('event_publish_or_draft');
            $table->integer('remaining_ticket');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
    }
};
