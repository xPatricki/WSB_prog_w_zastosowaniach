@extends('layouts.admin')

@section('admin-content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Manage Loans</h1>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.loans.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Book title, author, user name, email...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Currently Borrowed</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort By</label>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.loans.index', array_merge(request()->except(['sort_by', 'sort_direction']), ['sort_by' => 'due_at', 'sort_direction' => (request('sort_by') == 'due_at' && request('sort_direction', 'asc') == 'asc') ? 'desc' : 'asc'])) }}" class="btn btn-sm {{ request('sort_by', 'due_at') == 'due_at' ? (request('sort_direction', 'asc') == 'asc' ? 'btn-primary' : 'btn-info') : 'btn-outline-secondary' }}">
                            Due Date 
                            @if(request('sort_by', 'due_at') == 'due_at')
                                @if(request('sort_direction', 'asc') == 'asc')
                                    <i class="bi bi-arrow-up"></i>
                                @else
                                    <i class="bi bi-arrow-down"></i>
                                @endif
                            @endif
                        </a>
                        <a href="{{ route('admin.loans.index', array_merge(request()->except(['sort_by', 'sort_direction']), ['sort_by' => 'borrowed_at', 'sort_direction' => (request('sort_by') == 'borrowed_at' && request('sort_direction') == 'asc') ? 'desc' : 'asc'])) }}" class="btn btn-sm {{ request('sort_by') == 'borrowed_at' ? (request('sort_direction') == 'asc' ? 'btn-primary' : 'btn-info') : 'btn-outline-secondary' }}">
                            Borrow Date
                            @if(request('sort_by') == 'borrowed_at')
                                @if(request('sort_direction') == 'asc')
                                    <i class="bi bi-arrow-up"></i>
                                @else
                                    <i class="bi bi-arrow-down"></i>
                                @endif
                            @endif
                        </a>
                    </div>
                </div>
                <!-- Apply Filters button removed - form auto-submits on change -->
            </form>
            
            <script>
                // Auto-submit form when search or status filters change
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('search');
                    const statusSelect = document.getElementById('status');
                    
                    // Add debounce for search input (wait 500ms after typing stops)
                    let searchTimeout;
                    searchInput.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function() {
                            searchInput.form.submit();
                        }, 500);
                    });
                    
                    // Status filter changes submit immediately
                    statusSelect.addEventListener('change', function() {
                        this.form.submit();
                    });
                });
            </script>
        </div>
    </div>

    <!-- Loan Listing -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Book</th>
                            <th>User</th>
                            <th>Borrowed</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loans as $loan)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            @if($loan->book->cover_image)
                                                <img src="{{ asset('storage/' . $loan->book->cover_image) }}" alt="{{ $loan->book->title }}" class="img-thumbnail" style="width: 50px; height: 70px; object-fit: cover;">
                                            @elseif(isset($loan->book->cover_image_url))
                                                <img src="{{ $loan->book->cover_image_url }}" alt="{{ $loan->book->title }}" class="img-thumbnail" style="width: 50px; height: 70px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 70px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-book text-muted" viewBox="0 0 16 16">
                                                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="fw-medium mb-0">{{ $loan->book->title }}</p>
                                            <small class="text-muted">{{ $loan->book->author }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0">{{ $loan->user->name }}</p>
                                    <small class="text-muted">{{ $loan->user->email }}</small>
                                </td>
                                <td>{{ $loan->borrowed_at->format('M d, Y') }}</td>
                                <td>
                                    @if(!$loan->returned_at)
                                        <div @class([
                                            'fw-medium',
                                            'text-danger' => $loan->due_at->isPast(),
                                            'text-warning' => !$loan->due_at->isPast() && $loan->due_at->diffInDays(now()) <= 3,
                                        ])>
                                            {{ $loan->due_at->format('M d, Y') }}
                                            @if($loan->due_at->isPast())
                                                <span class="badge bg-danger">Overdue</span>
                                            @elseif($loan->due_at->diffInDays(now()) <= 3)
                                                <span class="badge bg-warning text-dark">Due Soon</span>
                                            @endif
                                        </div>
                                        <small>
                                            @if($loan->due_at->isPast())
                                                {{ $loan->due_at->diffForHumans() }} overdue
                                            @else
                                                {{ $loan->due_at->diffForHumans() }} remaining
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">{{ $loan->due_at->format('M d, Y') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->returned_at)
                                        <span class="badge bg-success">Returned on {{ $loan->returned_at->format('M d, Y') }}</span>
                                    @else
                                        <span class="badge bg-primary">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$loan->returned_at)
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modifyDueDateModal{{ $loan->id }}">Modify Due Date</button>
                                            <form action="{{ route('admin.loans.cancel', $loan) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this loan?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Cancel Loan</button>
                                            </form>
                                        </div>

                                        <!-- Due Date Modification Modal -->
                                        <div class="modal fade" id="modifyDueDateModal{{ $loan->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.loans.modify-due-date', $loan) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Modify Due Date</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="due_at" class="form-label">New Due Date</label>
                                                                <input type="date" class="form-control" id="due_at" name="due_at" value="{{ $loan->due_at->format('Y-m-d') }}" min="{{ $loan->borrowed_at->format('Y-m-d') }}" required>
                                                                <div class="form-text">
                                                                    Current due date: {{ $loan->due_at->format('M d, Y') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No actions available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-inbox mb-3" viewBox="0 0 16 16">
                                            <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4H4.98zM3.81.563A1.5 1.5 0 0 1 4.98 0h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a.5.5 0 0 1 .106.374l-.39 3.124A1.5 1.5 0 0 1 14.117 10H1.883A1.5 1.5 0 0 1 .394 8.686l-.39-3.124a.5.5 0 0 1 .106-.374L3.81.563zM.125 11.17A.5.5 0 0 1 .5 11H6a.5.5 0 0 1 .5.5 1.5 1.5 0 0 0 3 0 .5.5 0 0 1 .5-.5h5.5a.5.5 0 0 1 .496.562l-.39 3.124A1.5 1.5 0 0 1 14.117 16H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .121-.393z"/>
                                        </svg>
                                        <p>No loans found matching your criteria.</p>
                                        @if(request('search') || request('status') != 'active')
                                            <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-outline-secondary mt-2">Clear Filters</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $loans->links() }}
    </div>
</div>
@endsection
