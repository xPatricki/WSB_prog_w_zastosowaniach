@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1>Browse Books</h1>
    <p class="text-muted">Explore our collection of books available for borrowing.</p>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('books.index') }}" method="GET" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="search" class="form-control" placeholder="Search by title or author..." name="search" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    @forelse($books as $book)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="book-cover">
                    @if($book->cover_image)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="img-fluid" style="max-height: 100%;">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    @endif
                </div>
                <div class="card-body">
                    <h5 class="card-title text-truncate">{{ $book->title }}</h5>
                    <p class="card-text text-muted">{{ $book->author }}</p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-primary">View Details</a>
                    @auth
                        @if($book->status === 'available')
                            <form action="{{ route('books.borrow', $book) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">Borrow</button>
                            </form>
                        @else
                            <span class="badge bg-secondary">Unavailable</span>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Login to Borrow</a>
                    @endauth
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                No books found matching your criteria. Try adjusting your search.
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $books->links() }}
</div>
@endsection

