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
        // Tabla de temas del curso
        Schema::create('course_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla de contenidos de cada tema
        Schema::create('topic_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('course_topics')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'document', 'presentation', 'text'])->default('text');
            $table->string('file_path')->nullable(); // Para archivos subidos
            $table->text('content')->nullable(); // Para contenido de texto
            $table->integer('order')->default(1);
            $table->integer('duration_minutes')->nullable(); // Duración estimada
            $table->timestamps();
        });

        // Tabla de actividades
        Schema::create('topic_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('course_topics')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['quiz_multiple', 'quiz_open', 'essay', 'assignment'])->default('quiz_multiple');
            $table->json('content'); // Almacena preguntas, opciones, etc.
            $table->integer('max_attempts')->default(3);
            $table->integer('time_limit_minutes')->nullable();
            $table->decimal('max_score', 5, 2)->default(100);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        // Tabla de respuestas de estudiantes
        Schema::create('student_activity_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('topic_activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('responses'); // Respuestas del estudiante
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('feedback')->nullable(); // Retroalimentación del instructor
            $table->timestamps();
        });

        // Tabla de progreso del estudiante por tema
        Schema::create('student_topic_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('course_topics')->onDelete('cascade');
            $table->boolean('content_completed')->default(false);
            $table->boolean('activities_completed')->default(false);
            $table->decimal('topic_score', 5, 2)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_topic_progress');
        Schema::dropIfExists('student_activity_responses');
        Schema::dropIfExists('topic_activities');
        Schema::dropIfExists('topic_contents');
        Schema::dropIfExists('course_topics');
    }
};