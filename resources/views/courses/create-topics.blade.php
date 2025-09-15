@extends('layouts.app')

@section('title', 'Agregar Contenido - ' . $course->title)

@section('content')
<div class="container">
    <div class="header">
        <h1>Agregar Contenido al Curso: {{ $course->title }}</h1>
        <a href="{{ route('courses.manage-topics', $course) }}" class="btn btn-secondary">
            ← Volver a Temas
        </a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('courses.content.store', $course) }}" enctype="multipart/form-data">
            @csrf

            <!-- Selección de Tema -->
            <div class="form-group">
                <label for="topic_id">Tema *</label>
                <select name="topic_id" id="topic_id" class="form-control" required>
                    <option value="">Seleccionar tema</option>
                    @foreach($topics as $topicItem)
                        <option value="{{ $topicItem->id }}" {{ $topic && $topic->id == $topicItem->id ? 'selected' : '' }}>
                            {{ $topicItem->title }}
                        </option>
                    @endforeach
                </select>
                @error('topic_id')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Título -->
            <div class="form-group">
                <label for="title">Título del Contenido *</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                @error('title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tipo de Contenido -->
            <div class="form-group">
                <label for="type">Tipo de Contenido *</label>
                <select name="type" id="type" class="form-control" required onchange="toggleFields()">
                    <option value="">Seleccionar tipo</option>
                    <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>🎥 Video</option>
                    <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>📄 Documento</option>
                    <option value="presentation" {{ old('type') == 'presentation' ? 'selected' : '' }}>📊 Presentación</option>
                    <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>📝 Texto</option>
                </select>
                @error('type')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Archivo (para video, document, presentation) -->
            <div class="form-group" id="file-group">
                <label for="file">Archivo</label>
                <input type="file" name="file" id="file" class="form-control" 
                       accept=".mp4,.avi,.mov,.pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.ppt,.pptx">
                @error('file')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contenido de Texto -->
            <div class="form-group" id="text-content-group" style="display: none;">
                <label for="content">Contenido de Texto</label>
                <textarea name="content" id="content" class="form-control" rows="10">{{ old('content') }}</textarea>
                @error('content')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Duración -->
            <div class="form-group">
                <label for="duration_minutes">Duración (minutos)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" 
                       class="form-control" value="{{ old('duration_minutes') }}" min="1">
                @error('duration_minutes')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Orden -->
            <div class="form-group">
                <label for="order">Orden *</label>
                <input type="number" name="order" id="order" class="form-control" 
                       value="{{ old('order', 0) }}" min="0" required>
                @error('order')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Contenido</button>
                <a href="{{ route('courses.manage-topics', $course) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleFields() {
    const type = document.getElementById('type').value;
    const fileGroup = document.getElementById('file-group');
    const textGroup = document.getElementById('text-content-group');

    if (type === 'text') {
        fileGroup.style.display = 'none';
        textGroup.style.display = 'block';
    } else {
        fileGroup.style.display = 'block';
        textGroup.style.display = 'none';
    }
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>
@endpush

<style>
.container { max-width: 800px; margin: 0 auto; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.form-group { margin-bottom: 20px; }
.form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
.error { color: #dc3545; font-size: 14px; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px; }
.btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; }
.btn-primary { background: #007bff; color: white; }
.btn-secondary { background: #6c757d; color: white; }
</style>
@endsection