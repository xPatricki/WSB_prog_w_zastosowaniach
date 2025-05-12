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
                    <a href="https://www.google.com/maps/dir/?api=1&destination=52.40456833352429,16.922126635948416" target="_blank" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Get Directions
                    </a>
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
                    <div class="book-cover position-relative" style="height:120px;display:flex;align-items:center;justify-content:center;background:#f7f7f9;">
    @php
        // Try the same logic as the browse/details page for consistency
        $coverUrl = $book->cover_image_url ?? null;
        if (!$coverUrl && !empty($book->cover_image)) {
            // Try both possible storage paths
            if (file_exists(public_path('storage/covers/' . $book->cover_image))) {
                $coverUrl = asset('storage/covers/' . $book->cover_image);
            } elseif (file_exists(public_path('storage/' . $book->cover_image))) {
                $coverUrl = asset('storage/' . $book->cover_image);
            }
        }
        if (!$coverUrl) {
            $coverUrl = asset('images/placeholder-book.jpg');
        }
    @endphp
    <span class="cover-spinner position-absolute top-50 start-50 translate-middle" style="z-index:2;display:block;width:2rem;height:2rem;" aria-hidden="true">
      <svg viewBox="0 0 50 50" style="width:2rem;height:2rem;display:block;">
        <circle cx="25" cy="25" r="20" fill="none" stroke="#222" stroke-width="5" stroke-linecap="round" stroke-dasharray="90 60"/>
      </svg>
    </span>
    @if($coverUrl)
    <img src="{{ $coverUrl }}" alt="{{ $book->title }}" style="width:100%;height:120px;object-fit:cover;display:none;" onload="this.style.display='block';this.parentNode.querySelector('.cover-spinner').style.display='none';this.parentNode.parentNode.querySelector('.card-title').style.visibility='visible';" onerror="this.style.display='none';this.parentNode.querySelector('.cover-spinner').style.display='none';this.parentNode.querySelector('.img-error').style.display='block';this.parentNode.parentNode.querySelector('.card-title').style.visibility='visible';">
    <span class="img-error position-absolute top-50 start-50 translate-middle text-danger" style="display:none;z-index:3;">
        <svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'><rect x='3' y='3' width='18' height='18' rx='2' fill='#fff'/><line x1='3' y1='3' x2='21' y2='21' stroke='red' stroke-width='2'/></svg>
    </span>
    @else
        @php
            $letters = strtoupper(mb_substr($book->title, 0, 2));
            $bg = ['#6c757d','#007bff','#6610f2','#fd7e14','#28a745','#dc3545','#20c997','#17a2b8'];
            $color = $bg[$book->id % count($bg)];
        @endphp
        <svg width="90" height="100" xmlns="http://www.w3.org/2000/svg">
            <rect width="90" height="100" rx="12" fill="{{ $color }}"/>
            <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="36" fill="#fff" font-family="Arial, sans-serif">{{ $letters }}</text>
        </svg>
        <script>document.currentScript.parentNode.querySelector('.cover-spinner').style.display='none';document.currentScript.parentNode.parentNode.querySelector('.card-title').style.visibility='visible';</script>
    @endif
</div>
<style>
@keyframes spin-cascade{100%{transform:rotate(360deg);}}
.cover-spinner svg{animation:spin-cascade 0.8s linear infinite;}
</style>
<div class="card-body">
    <h5 class="card-title text-truncate" style="visibility:hidden;">{{ $book->title }}</h5>
                        <p class="card-text text-muted">{{ $book->author }}</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-primary">View Details</a>
                        @auth
                            @if(auth()->user()->role === 'user' && $book->status === 'available')
                                <form action="{{ route('books.borrow', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">Borrow</button>
                                </form>
                            @elseif($book->status !== 'available')
                                <span class="badge bg-secondary">Unavailable</span>
                            @endif
                        @else
                            <small class="text-muted">Login to borrow</small>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

