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
            $table->string('event_refund');
            $table->string('event_category');
            $table->string('event_sub_category');
            $table->string('event_code')->nullable();
            $table->string('event_organizer');
            $table->date('event_start_date');
            $table->time('event_start_time');
            $table->date('event_end_date');
            $table->time('event_end_time');
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
