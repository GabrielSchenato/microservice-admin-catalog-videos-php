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
        Schema::create('cast_member_video', function (Blueprint $table) {
            $table->uuid('cast_member_id')->index();
            $table->foreign('cast_member_id')
                ->references('id')
                ->on('cast_members');
            $table->uuid('video_id')->index();
            $table->foreign('video_id')
                ->references('id')
                ->on('videos');

            $table->unique(['cast_member_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cast_member_video');
    }
};
