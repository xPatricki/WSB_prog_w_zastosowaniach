@extends('layouts.admin')

@section('admin-content')
<div class="mb-4">
    <h1>Add New Book</h1>
    <p class="text-muted">Fill in the details for the new book.</p>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3 row">
                <label for="isbn" class="col-sm-3 col-form-label text-md-end my-auto">
                    ISBN <span class="text-danger">*</span>
                    <i class="ms-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="ISBN number (unique identifier for the book)." style="cursor: help;">&#63;</i>
                </label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn') }}" required>
                        <button type="button" class="btn btn-outline-info" id="sync-isbn-btn">Sync</button>
                    </div>
                    @error('isbn')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-7 offset-sm-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sync_enabled" name="sync_enabled" checked>
                        <label class="form-check-label" for="sync_enabled">
                            Synchronize with external source
                        </label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="title" class="col-sm-3 col-form-label text-md-end">Title <span class="text-danger">*</span></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3 row">
                <label for="author" class="col-sm-3 col-form-label text-md-end">Author <span class="text-danger">*</span></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control @error('author') is-invalid @enderror" id="author" name="author" value="{{ old('author') }}" required>
                    @error('author')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            

            
            <div class="mb-3 row">
                <label for="description" class="col-sm-3 col-form-label text-md-end">Description</label>
                <div class="col-sm-7">
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3 row">
                <label for="cover_image" class="col-sm-3 col-form-label text-md-end">
                    Cover Image
                    <i class="ms-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload a file or provide a URL. Images are automatically optimized." style="cursor: help;">&#63;</i>
                </label>
                <div class="col-sm-7">
                    <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image">
                    <div class="form-text">Optional. Max file size: 2MB. Supported formats: JPG, PNG.</div>
                    <label for="cover_image_url" class="form-label mt-2">Cover Image URL (auto-filled by Sync or paste manually)</label>
<input type="url" class="form-control" id="cover_image_url" name="cover_image_url" placeholder="Paste cover image URL or use Sync" value="{{ old('cover_image_url') }}">
                    <div id="cover_image_preview" class="mt-2"></div>
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <label for="quantity" class="col-sm-3 col-form-label text-md-end">Quantity <span class="text-danger">*</span></label>
                <div class="col-sm-7">
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                    <div class="form-text">Number of copies available for this book.</div>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3 row">
                <div class="col-sm-7 offset-sm-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured" {{ old('featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="featured">
                            Featured book (displayed on homepage)
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-sm-7 offset-sm-3">
                    <button type="submit" class="btn btn-primary">Add Book</button>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="/book_sync.js"></script>

<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection
