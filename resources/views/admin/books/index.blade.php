@extends('layouts.admin')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Books Management</h1>
        <p class="text-muted">Add, edit, and manage books in the library.</p>
    </div>
    <div>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Book
        </a>
        <button type="button" class="btn btn-outline-info me-2" id="sync-selected-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.13-3.36L23 10"></path><path d="M20.49 15a9 9 0 0 1-14.13 3.36L1 14"></path></svg>
            Sync Selected
        </button>
        <button type="button" class="btn btn-outline-danger" id="delete-selected-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
            Delete Selected
        </button>
    </div>
<script>
// Bulk select
const selectAll = document.getElementById('select-all-books');
const checkboxes = document.querySelectorAll('.select-book');
if (selectAll) {
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });
}
// TODO: Wire up Sync Selected and Delete Selected actions
</script>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-books"></th>
<th>Cover</th>
<th>Title</th>
<th>Author</th>
<th>Quantity</th>
<th>Status</th>
<th>Featured</th>
<th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
@php
    $activeLoans = $book->loans()->whereNull('returned_at')->count();
    $available = $book->quantity - $activeLoans;
    $coverUrl = $book->cover_image_url ?: ($book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/placeholder-book.jpg'));
@endphp
<tr @if($available < 1) style="background-color: #f8f9fa; color: #999;" @endif>
    <td><input type="checkbox" class="select-book" value="{{ $book->id }}"></td>
    <td><img src="{{ $coverUrl }}" alt="Cover" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;"></td>
    <td class="fw-medium">{{ $book->title }}</td>
    <td>{{ $book->author }}</td>
    <td>
        @if($available > 0)
            <span class="badge bg-success">{{ $available }} available</span>
        @else
            <span class="badge bg-secondary">Unavailable</span>
        @endif
    </td>
    <td>
        <span class="badge {{ $book->status === 'available' && $available > 0 ? 'bg-success' : 'bg-secondary' }}">
            {{ $book->status }}
        </span>
    </td>
                        <td>
                            @if($book->featured)
                                <span class="badge bg-primary">Featured</span>
                            @else
                                <span class="badge bg-light text-dark">No</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-sm btn-outline-secondary me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    <span class="visually-hidden">Edit</span>
                                </a>
                                <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this book?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        <span class="visually-hidden">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $books->links() }}
    </div>
</div>
@endsection
