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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_title');
            $table->boolean('event_type')->nullable();
            $table->string('event_location')->nullable();
            $table->string('event_link')->nullable();
            $table->text('event_note')->nullable();
            $table->text('event_description');
            $table->text('event_address');
            $table->string('event_refund');
            $table->string('event_category');
            $table->string('event_status');
            $table->string('event_code')->nullable();
            $table->string('event_organizer');
            $table->date('start_date');
            $table->date('start_time');
            $table->date('end_date');
            $table->date('end_time');
            $table->integer('event_capacity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
