@extends('layouts.admin')

@section('admin-content')
<div class="mb-4">
    <h1>Admin Dashboard</h1>
    <p class="text-muted">Overview of library statistics and management.</p>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab">Overview</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="analytics-tab" data-bs-toggle="tab" href="#analytics" role="tab">Analytics</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="reports-tab" data-bs-toggle="tab" href="#reports" role="tab">Reports</a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total Books</h5>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0   stroke-linejoin="round" class="text-muted"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    </div>
                    <div class="card-body">
                        <h2 class="display-6">{{ $stats['totalBooks'] }}</h2>
                        <p class="text-muted small">+{{ $stats['bookGrowth'] }}% since last month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Registered Users</h5>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div class="card-body">
                        <h2 class="display-6">{{ $stats['registeredUsers'] }}</h2>
                        <p class="text-muted small">+{{ $stats['userGrowth'] }}% since last month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Active Loans</h5>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                    <div class="card-body">
                        <h2 class="display-6">{{ $stats['activeLoans'] }}</h2>
                        <p class="text-muted small">+19% from last month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Overdue Books</h5>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div class="card-body">
                        <h2 class="display-6">{{ $stats['overdueBooks'] }}</h2>
                        <p class="text-muted small">-12% from last month</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($recentActivity as $activity)
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 fw-medium">
                                            {{ $activity->status === 'active' ? 'Book borrowed' : 'Book returned' }}
                                        </p>
                                        <p class="text-muted small mb-0">
                                            {{ $activity->user->name }} {{ $activity->status === 'active' ? 'borrowed' : 'returned' }} {{ $activity->book->title }}
                                        </p>
                                    </div>
                                    <div class="text-muted small">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Popular Books</h5>
                        <p class="text-muted small mb-0">Most borrowed books this month</p>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($popularBooks as $book)
                                <div class="list-group-item px-0">
                                    <p class="mb-0 fw-medium">{{ $book->title }}</p>
                                    <p class="text-muted small mb-0">Borrowed {{ $book->loans_count }} times</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="analytics" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Analytics</h5>
                <p class="text-muted small mb-0">Detailed analytics will be displayed here.</p>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="height: 400px;">
                <p class="text-muted">Analytics charts and graphs will be displayed here.</p>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="reports" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Reports</h5>
                <p class="text-muted small mb-0">Generate and view reports.</p>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="height: 400px;">
                <p class="text-muted">Reports will be displayed here.</p>
            </div>
        </div>
    </div>
</div>
@endsection

