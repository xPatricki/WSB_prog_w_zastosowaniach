@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1>Welcome to the Library</h1>
    <p class="text-muted">Browse our collection of books and manage your reading list.</p>
</div>

@guest
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Browse Collection</h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                </div>
                <div class="card-body">
                    <h2 class="h4">5,000+ Books</h2>
                    <p class="text-muted small">Available in our library</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('books.index') }}" class="btn btn-primary w-100">Browse Books</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Account</h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div class="card-body">
                    <h2 class="h4">Join Today</h2>
                    <p class="text-muted small">Sign up to rent books and track your reading</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('register') }}" class="btn btn-primary w-100">Sign Up</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Opening Hours</h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="card-body">
                    <h2 class="h4">9AM - 8PM</h2>
                    <p class="text-muted small">Monday to Saturday, Closed on Sundays</p>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-outline-secondary w-100">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Books</h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <div class="card-body">
                    <a href="{{ route('loans.index') }}" class="btn btn-primary">View My Books</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Browse Books</h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                </div>
                <div class="card-body">
                    <a href="{{ route('books.index') }}" class="btn btn-primary">Browse Collection</a>
                </div>
            </div>
        </div>
    </div>
@endguest

<div class="mt-5">
    <h2>Featured Books</h2>
    <div class="row">
        @foreach($featuredBooks as $book)
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
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

