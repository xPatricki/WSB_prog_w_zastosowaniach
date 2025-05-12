@extends('layouts.app')

@section('content')
<div class="container">
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
            @if($currentLoans->isEmpty())
                <div class="alert alert-info">
                    You don't have any borrowed books at the moment.
                </div>
            @else
                <div class="row">
                    @foreach($currentLoans as $loan)
                        <div class="col-md-4 col-sm-12 col-12 mb-4" style="max-width: 33.33%;">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ $loan->book->title }}</h5>
                                    <p class="text-muted mb-0">{{ $loan->book->author }}</p>
                                </div>
                                <div class="text-center p-3 bg-light">
                                    @if($loan->book->cover_image)
                                        <img src="{{ asset('storage/' . $loan->book->cover_image) }}" alt="{{ $loan->book->title }}" class="img-fluid" style="max-height: 150px; width: auto;">
                                    @elseif($loan->book->cover_image_url)
                                        <img src="{{ $loan->book->cover_image_url }}" alt="{{ $loan->book->title }}" class="img-fluid" style="max-height: 150px; width: auto;">
                                    @else
                                        <div class="p-4 text-center text-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
                                                <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                                            </svg>
                                            <p class="mt-2">No cover available</p>
                                        </div>
                                    @endif
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
                                        <div class="countdown-timer" 
                                             data-overdue="{{ $loan->is_overdue ? 'true' : 'false' }}"
                                             data-days="{{ $loan->time_remaining['days'] }}"
                                             data-hours="{{ $loan->time_remaining['hours'] }}"
                                             data-minutes="{{ $loan->time_remaining['minutes'] }}"
                                             data-seconds="{{ $loan->time_remaining['seconds'] }}">
                                            @if($loan->is_overdue)
                                                <div class="text-danger d-flex align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                                    Overdue
                                                </div>
                                            @else
                                                <div class="row text-center">
                                                    <div class="col-3">
                                                        <div class="fw-bold fs-4">{{ $loan->time_remaining['days'] }}</div>
                                                        <div class="small text-muted">Days</div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="fw-bold fs-4">{{ $loan->time_remaining['hours'] }}</div>
                                                        <div class="small text-muted">Hours</div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="fw-bold fs-4">{{ $loan->time_remaining['minutes'] }}</div>
                                                        <div class="small text-muted">Mins</div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="fw-bold fs-4 seconds">{{ $loan->time_remaining['seconds'] }}</div>
                                                        <div class="small text-muted">Secs</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <form action="{{ route('my-books.return', $loan) }}" method="POST">
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
            @if($loanHistory->isEmpty())
                <div class="alert alert-info">
                    You don't have any loan history yet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Cover</th>
                                <th>Borrowed</th>
                                <th>Returned</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loanHistory as $loan)
                                <tr>
                                    <td>
                                        <strong>{{ $loan->book->title }}</strong>
                                        <div class="text-muted small">{{ $loan->book->author }}</div>
                                    </td>
                                    <td style="width: 80px;">
                                        @if($loan->book->cover_image)
                                            <img src="{{ asset('storage/' . $loan->book->cover_image) }}" alt="{{ $loan->book->title }}" class="img-fluid" style="max-height: 60px; width: auto;">
                                        @elseif($loan->book->cover_image_url)
                                            <img src="{{ $loan->book->cover_image_url }}" alt="{{ $loan->book->title }}" class="img-fluid" style="max-height: 60px; width: auto;">
                                        @else
                                            <div class="text-center text-muted">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
                                                    <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $loan->borrowed_at->format('M d, Y') }}</td>
                                    <td>{{ $loan->returned_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-success">Returned on time</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('books.borrow', $loan->book) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Borrow Again</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownTimers = document.querySelectorAll('.countdown-timer');
        
        countdownTimers.forEach(timer => {
            if (timer.dataset.overdue === 'true') {
                return;
            }
            
            let days = parseInt(timer.dataset.days);
            let hours = parseInt(timer.dataset.hours);
            let minutes = parseInt(timer.dataset.minutes);
            let seconds = parseInt(timer.dataset.seconds);
            
            const secondsElement = timer.querySelector('.seconds');
            
            const interval = setInterval(() => {
                seconds--;
                
                if (seconds < 0) {
                    seconds = 59;
                    minutes--;
                    
                    if (minutes < 0) {
                        minutes = 59;
                        hours--;
                        
                        if (hours < 0) {
                            hours = 23;
                            days--;
                            
                            if (days < 0) {
                                clearInterval(interval);
                                timer.innerHTML = `
                                    <div class="text-danger d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                        Overdue
                                    </div>
                                `;
                                return;
                            }
                        }
                    }
                }
                
                secondsElement.textContent = seconds.toString().padStart(2, '0');
            }, 1000);
        });
    });
</script>
@endpush
@endsection

