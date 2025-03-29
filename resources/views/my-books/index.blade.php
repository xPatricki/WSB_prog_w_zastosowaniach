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
                <div class="row">
                    @foreach($loanHistory as $loan)
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
                                        <button type="submit" class="btn btn-outline-primary w-100">Borrow Again</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
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

