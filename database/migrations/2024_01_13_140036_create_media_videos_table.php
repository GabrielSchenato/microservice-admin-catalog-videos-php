<?php

use App\Enums\MediaTypes;
use Core\Domain\Enum\MediaStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media_videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('video_id')->index();
            $table->foreign('video_id')->references('id')->on('videos');
            $table->string('file_path');
            $table->string('encoded_path')->nullable();
            $table->enum('media_status', array_keys(MediaStatus::cases()))
                ->default(MediaStatus::COMPLETE->value);
            $table->enum('type', array_keys(MediaTypes::cases()));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
