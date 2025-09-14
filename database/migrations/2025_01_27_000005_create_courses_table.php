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
        // Tabla de cursos
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->integer('credits')->default(0);
            $table->integer('duration_hours')->default(0);
            $table->enum('difficulty', ['basico', 'intermedio', 'avanzado'])->default('basico');
            $table->enum('status', ['activo', 'inactivo', 'borrador'])->default('borrador');
            $table->string('image')->nullable();
            $table->integer('max_students')->default(30);
            $table->integer('min_students')->default(5);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabla de prerrequisitos
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('prerequisite_course_id')->constrained('courses')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['course_id', 'prerequisite_course_id']);
        });

        // Tabla de inscripciones
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['inscrito', 'completado', 'abandonado', 'pendiente'])->default('inscrito');
            $table->decimal('grade', 5, 2)->nullable();
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_prerequisites');
        Schema::dropIfExists('courses');
    }
};