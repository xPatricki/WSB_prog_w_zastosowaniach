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
                <div class="book-cover position-relative" style="width:100%;height:220px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#f7f7f7;">
                    @php
                        $coverUrl = $book->cover_image_url ?: ($book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/placeholder-book.jpg'));
                    @endphp
                    <span class="cover-spinner position-absolute top-50 start-50 translate-middle" style="z-index:2;display:block;width:2rem;height:2rem;" aria-hidden="true">
  <svg viewBox="0 0 50 50" style="width:2rem;height:2rem;display:block;">
    <circle cx="25" cy="25" r="20" fill="none" stroke="#222" stroke-width="5" stroke-linecap="round" stroke-dasharray="90 60"/>
  </svg>
</span>
<style>
@keyframes spin-cascade {
  100% { transform: rotate(360deg); }
}
.cover-spinner svg {
  animation: spin-cascade 0.8s linear infinite;
}
</style>
                    <img src="{{ $coverUrl }}" alt="{{ $book->title }}" style="width:100%;height:100%;object-fit:cover;display:none;" onload="this.style.display='block';this.parentNode.querySelector('.cover-spinner').style.display='none';this.parentNode.parentNode.querySelector('.card-title').style.visibility='visible';" onerror="this.style.display='none';this.parentNode.querySelector('.cover-spinner').style.display='none';this.parentNode.querySelector('.img-error').style.display='block';this.parentNode.parentNode.querySelector('.card-title').style.visibility='visible';">
                    <span class="img-error position-absolute top-50 start-50 translate-middle text-danger" style="display:none;z-index:3;">
                        <svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'><rect x='3' y='3' width='18' height='18' rx='2' fill='#fff'/><line x1='3' y1='3' x2='21' y2='21' stroke='red' stroke-width='2'/></svg>
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-truncate" style="visibility:hidden;">{{ $book->title }}</h5>
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

