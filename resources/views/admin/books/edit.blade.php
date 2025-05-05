@extends('layouts.admin')

@section('admin-content')
<div class="mb-4">
    <h1>Edit Book</h1>
    <p class="text-muted">Make changes to the book details below.</p>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-3 row">
                <label for="title" class="col-sm-3 col-form-label text-md-end">Title</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $book->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3 row">
                <label for="author" class="col-sm-3 col-form-label text-md-end">Author</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control @error('author') is-invalid @enderror" id="author" name="author" value="{{ old('author', $book->author) }}" required>
                    @error('author')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3 row">
                <label for="description" class="col-sm-3 col-form-label text-md-end">Description</label>
                <div class="col-sm-9">
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $book->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3 row">
                <label for="cover_image" class="col-sm-3 col-form-label text-md-end">Cover Image</label>
                <div class="col-sm-9">
                    @if($book->cover_image)
                        <div class="mb-2">
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    @endif
                    <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image">
                    <div class="form-text">Optional. Max file size: 2MB. Supported formats: JPG, PNG.</div>
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3 row">
                <div class="col-sm-9 offset-sm-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured" {{ old('featured', $book->featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="featured">
                            Featured book (displayed on homepage)
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary">Update Book</button>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
