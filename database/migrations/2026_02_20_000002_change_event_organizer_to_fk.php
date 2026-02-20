<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new column
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('organizer_id')->nullable()->after('event_organizer');
        });

        // Step 2: Migrate existing data
        DB::statement('
            UPDATE events
            SET organizer_id = (
                SELECT o.id FROM organizers o
                WHERE o.organizer_name = events.event_organizer
                LIMIT 1
            )
            WHERE event_organizer IS NOT NULL
        ');

        // Step 3: Drop old column and add FK constraint
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_organizer');
            $table->foreign('organizer_id')
                ->references('id')
                ->on('organizers')
                ->onDelete('set null');
            $table->index('organizer_id');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('event_organizer')->nullable()->after('organizer_id');
        });

        DB::statement('
            UPDATE events
            SET event_organizer = (
                SELECT o.organizer_name FROM organizers o
                WHERE o.id = events.organizer_id
                LIMIT 1
            )
            WHERE organizer_id IS NOT NULL
        ');

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['organizer_id']);
            $table->dropIndex(['organizer_id']);
            $table->dropColumn('organizer_id');
        });
    }
};
