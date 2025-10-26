<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('type'); // video, text, image, document
            $table->text('content_text')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_source_type')->nullable(); // youtube, vimeo, upload
            $table->string('video_length')->nullable();
            $table->string('video_path')->nullable();
            $table->string('image_path')->nullable();
            $table->string('document_path')->nullable();
            $table->string('column_position')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};