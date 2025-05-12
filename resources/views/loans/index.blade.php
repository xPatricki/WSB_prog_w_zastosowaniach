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
                    <div class="col-md-4 mb-4">
                        <div class="card h-100" style="min-height: 450px;">
                            <div class="card-header">
                                <h5 class="mb-0 text-truncate" title="{{ $loan->book->title }}">{{ $loan->book->title }}</h5>
                                <p class="text-muted mb-0 text-truncate" title="{{ $loan->book->author }}">{{ $loan->book->author }}</p>
                            </div>
                            <div class="text-center p-3 bg-light" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                @if(isset($loan->book->cover_image))
                                    <img src="{{ asset('storage/' . $loan->book->cover_image) }}" alt="{{ $loan->book->title }}" class="img-fluid" style="max-height: 150px; max-width: 100%; object-fit: contain;">
                                @elseif(isset($loan->book->cover_image_url))
                                    <img src="{{ $loan->book->cover_image_url }}" alt="{{ $loan->book->title }}" class="img-fluid" style="max-height: 150px; max-width: 100%; object-fit: contain;">
                                @else
                                    <div class="p-4 text-center text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
                                            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                                        </svg>
                                        <p class="mt-2">No cover available</p>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body" style="flex: 1 1 auto;">
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
                                <div class="d-flex gap-2">
                                    <a href="{{ route('books.show', $loan->book) }}" class="btn btn-primary flex-grow-1">View Details</a>
                                    <form action="{{ route('loans.return', $loan) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">Return Book</button>
                                    </form>
                                </div>
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
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Borrowed</th>
                            <th>Returned</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnedLoans as $loan)
                            <tr>
                                <td>
                                    <strong>{{ $loan->book->title }}</strong>
                                    <div class="text-muted small">{{ $loan->book->author }}</div>
                                </td>
                                <td>{{ $loan->borrowed_at->format('M d, Y') }}</td>
                                <td>{{ $loan->returned_at->format('M d, Y') }}</td>
                                <td>
                                    @if($loan->returned_at > $loan->due_at)
                                        @php
                                            $overdueDays = $loan->returned_at->diffInDays($loan->due_at);
                                            $overdueHours = $loan->returned_at->diffInHours($loan->due_at) % 24;
                                            $overdueTime = '';
                                            if ($overdueDays > 0) {
                                                $overdueTime .= $overdueDays . ' day' . ($overdueDays != 1 ? 's' : '');
                                            }
                                            if ($overdueHours > 0) {
                                                $overdueTime .= ($overdueDays > 0 ? ', ' : '') . $overdueHours . ' hour' . ($overdueHours != 1 ? 's' : '');
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-circle me-1"></i> Returned overdue
                                            </span>
                                            <span class="ms-1 text-muted" data-bs-toggle="tooltip" title="Overdue by {{ $overdueTime }}">
                                                <i class="bi bi-question-circle"></i>
                                            </span>
                                        </div>
                                    @else
                                        @php
                                            $earlyDays = $loan->due_at->diffInDays($loan->returned_at);
                                            $earlyHours = $loan->due_at->diffInHours($loan->returned_at) % 24;
                                            $earlyTime = '';
                                            if ($earlyDays > 0) {
                                                $earlyTime .= $earlyDays . ' day' . ($earlyDays != 1 ? 's' : '');
                                            }
                                            if ($earlyHours > 0) {
                                                $earlyTime .= ($earlyDays > 0 ? ', ' : '') . $earlyHours . ' hour' . ($earlyHours != 1 ? 's' : '');
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Returned on time
                                            </span>
                                            <span class="ms-1 text-muted" data-bs-toggle="tooltip" title="Returned {{ $earlyTime }} before due date">
                                                <i class="bi bi-question-circle"></i>
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('books.borrow', $loan->book) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" {{ $loan->book->status !== 'available' ? 'disabled' : '' }}>
                                            {{ $loan->book->status === 'available' ? 'Borrow Again' : 'Unavailable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Pagination Navigation -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $returnedLoans->fragment('history')->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Initialize tooltips and handle tab activation based on URL hash
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Check URL hash and activate the appropriate tab
        if (window.location.hash === '#history') {
            // Get the tab elements
            const historyTab = document.getElementById('history-tab');
            const historyPane = document.getElementById('history');
            const currentTab = document.getElementById('current-tab');
            const currentPane = document.getElementById('current');
            
            // Activate history tab
            historyTab.classList.add('active');
            historyPane.classList.add('show', 'active');
            
            // Deactivate current tab
            currentTab.classList.remove('active');
            currentPane.classList.remove('show', 'active');
        }
    });
</script>
@endsection
