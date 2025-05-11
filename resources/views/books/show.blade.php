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
                    <div class="book-cover mb-3 position-relative" style="width:100%;height:220px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#f7f7f7;">
                        @php
                            $coverUrl = $book->cover_image_url ?: ($book->cover_image ? asset('storage/' . $book->cover_image) : null);
                        @endphp
                        <span class="spinner-border spinner-border-sm position-absolute top-50 start-50 translate-middle" style="z-index:2;display:none;" aria-hidden="true"></span>
                        @if($coverUrl)
                        <img src="{{ $coverUrl }}" alt="{{ $book->title }}" style="width:100%;height:100%;object-fit:cover;display:block;" onload="this.previousElementSibling.style.display='none';this.style.display='block';" onerror="this.style.display='none';this.previousElementSibling.style.display='none';this.parentNode.querySelector('.img-error').style.display='block';">
                        <span class="img-error position-absolute top-50 start-50 translate-middle text-danger" style="display:none;z-index:3;">
                            <svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'><rect x='3' y='3' width='18' height='18' rx='2' fill='#fff'/><line x1='3' y1='3' x2='21' y2='21' stroke='red' stroke-width='2'/></svg>
                        </span>
                        @else
                        <span class="img-error position-absolute top-50 start-50 translate-middle text-danger" style="display:block;z-index:3;">
                            <svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'><rect x='3' y='3' width='18' height='18' rx='2' fill='#fff'/><line x1='3' y1='3' x2='21' y2='21' stroke='red' stroke-width='2'/></svg>
                        </span>
                        @endif
                    </div>
                    
                    @php
    $activeLoans = $book->loans()->whereNull('returned_at')->count();
    $available = $book->quantity - $activeLoans;
@endphp
<div class="mb-2">
    <span class="badge bg-info">{{ $available }} available</span>
</div>
@auth
    @if($book->status === 'available' && $available > 0)
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

