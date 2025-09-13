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
        Schema::table('users', function (Blueprint $table) {
            // Información académica
            $table->string('carrera')->nullable()->after('email');
            $table->string('matricula')->nullable()->unique()->after('carrera');
            $table->integer('semestre')->nullable()->after('matricula');
            
            // Información de contacto
            $table->string('telefono')->nullable()->after('semestre');
            $table->string('curp')->nullable()->after('telefono');
            $table->date('fecha_nacimiento')->nullable()->after('curp');
            $table->integer('edad')->nullable()->after('fecha_nacimiento');
            
            // Dirección
            $table->string('colonia')->nullable()->after('edad');
            $table->string('calle')->nullable()->after('colonia');
            $table->string('ciudad')->nullable()->after('calle');
            $table->string('estado')->nullable()->after('ciudad');
            $table->string('codigo_postal')->nullable()->after('estado');
            
            // Foto de perfil
            $table->string('foto_perfil')->nullable()->after('codigo_postal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'carrera', 'matricula', 'semestre', 'telefono', 'curp', 
                'fecha_nacimiento', 'edad', 'colonia', 'calle', 'ciudad', 
                'estado', 'codigo_postal', 'foto_perfil'
            ]);
        });
    }
};