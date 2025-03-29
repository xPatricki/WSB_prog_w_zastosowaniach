@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Back to Books
    </a>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="book-cover mb-3">
                        @if($book->cover_image)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="img-fluid">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        @endif
                    </div>
                    
                    @auth
                        @if($book->status === 'available')
                            <form action="{{ route('books.borrow', $book) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2">Borrow This Book</button>
                            </form>
                        @else
                            <button class="btn btn-secondary w-100 mb-2" disabled>Currently Unavailable</button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Login to Borrow</a>
                    @endauth
                </div>
                
                <div class="col-md-9">
                    <h1>{{ $book->title }}</h1>
                    <h5 class="text-muted">by {{ $book->author }}</h5>
                    
                    <div class="mb-3">
                        <span class="badge bg-{{ $book->status === 'available' ? 'success' : 'secondary' }}">
                            {{ $book->status === 'available' ? 'Available' : 'Borrowed' }}
                        </span>
                    </div>
                    
                    <h4>Description</h4>
                    <p>{{ $book->description ?? 'No description available.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

