@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1>My Books</h1>
    <p class="text-muted">Manage your borrowed books and view your reading history.</p>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" id="current-tab" data-bs-toggle="tab" href="#current" role="tab">Currently Borrowed</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history" role="tab">History</a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="current" role="tabpanel">
        @if($activeLoans->isEmpty())
            <div class="alert alert-info">
                You don't have any borrowed books at the moment.
                <a href="{{ route('books.index') }}" class="alert-link">Browse books</a> to borrow some.
            </div>
        @else
            <div class="row">
                @foreach($activeLoans as $loan)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ $loan->book->title }}</h5>
                                <p class="text-muted mb-0">{{ $loan->book->author }}</p>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Borrowed:</div>
                                    <div class="col-sm-8">{{ $loan->borrowed_at->format('M d, Y') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Due:</div>
                                    <div class="col-sm-8">{{ $loan->due_at->format('M d, Y') }}</div>
                                </div>
                                <div class="mt-4">
                                    <div class="mb-2 fw-medium">Time Remaining:</div>
                                    <div class="countdown-timer" data-due-date="{{ $loan->due_at->toISOString() }}">
                                        @if($loan->is_overdue)
                                            <span class="overdue">Overdue</span>
                                        @else
                                            {{ $loan->time_remaining['days'] }} days, {{ $loan->time_remaining['hours'] }} hours
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('loans.return', $loan) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">Return Book</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="tab-pane fade" id="history" role="tabpanel">
        @if($returnedLoans->isEmpty())
            <div class="alert alert-info">
                You don't have any loan history yet.
            </div>
        @else
            <div class="row">
                @foreach($returnedLoans as $loan)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ $loan->book->title }}</h5>
                                <p class="text-muted mb-0">{{ $loan->book->author }}</p>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Borrowed:</div>
                                    <div class="col-sm-8">{{ $loan->borrowed_at->format('M d, Y') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Returned:</div>
                                    <div class="col-sm-8">{{ $loan->returned_at->format('M d, Y') }}</div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center mt-4 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    <span>Returned on time</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('books.borrow', $loan->book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary w-100" {{ $loan->book->status !== 'available' ? 'disabled' : '' }}>
                                        {{ $loan->book->status === 'available' ? 'Borrow Again' : 'Currently Unavailable' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

