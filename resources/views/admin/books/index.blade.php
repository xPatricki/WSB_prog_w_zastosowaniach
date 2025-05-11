@extends('layouts.admin')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Books Management</h1>
        <p class="text-muted">Add, edit, and manage books in the library.</p>
    </div>
    <div>
        <div class="btn-group me-2">
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Book
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkAddModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Bulk Add
                </a></li>
            </ul>
        </div>
        <button type="button" class="btn btn-outline-info me-2" id="sync-selected-btn" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.13-3.36L23 10"></path><path d="M20.49 15a9 9 0 0 1-14.13 3.36L1 14"></path></svg>
            Sync Selected
        </button>
        <button type="button" class="btn btn-outline-danger" id="delete-selected-btn" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
            Delete Selected
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const selectAll = document.getElementById('select-all-books');
    const syncBtn = document.getElementById('sync-selected-btn');
    const deleteBtn = document.getElementById('delete-selected-btn');

    // Helper function to get all selected book IDs
    function getSelectedBookIds() {
        return Array.from(document.querySelectorAll('.select-book:checked')).map(cb => cb.value);
    }

    // Update bulk action buttons based on checkbox state
    function updateBulkButtons() {
        const checkboxes = document.querySelectorAll('.select-book');
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        syncBtn.disabled = !anyChecked;
        deleteBtn.disabled = !anyChecked;
        syncBtn.classList.toggle('disabled', !anyChecked);
        deleteBtn.classList.toggle('disabled', !anyChecked);
    }

    // Show alert helper function
    function showAlert(type, message) {
        let alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.role = 'alert';
        alert.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        document.body.prepend(alert);
        setTimeout(() => alert.remove(), 4000);
    }

    // Select All checkbox handler
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.select-book');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkButtons();
        });
    }

    // Row checkbox change handler - use event delegation
    document.addEventListener('change', function(e) {
        if (e.target.classList && e.target.classList.contains('select-book')) {
            updateBulkButtons();
            
            // Update Select All checkbox based on all row checkboxes
            const checkboxes = document.querySelectorAll('.select-book');
            const allChecked = checkboxes.length > 0 && 
                               Array.from(checkboxes).every(box => box.checked);
            selectAll.checked = allChecked;
        }
    });

    // Sync Selected button handler
    document.getElementById('sync-selected-btn').addEventListener('click', function() {
        const ids = getSelectedBookIds();
        if (ids.length === 0) {
            showAlert('warning', 'No books selected.');
            return;
        }
        
        fetch("{{ route('admin.books.bulkSync') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ids})
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            } else {
                showAlert('danger', data.message);
            }
        });
    });

    // Delete Selected button handler
    document.getElementById('delete-selected-btn').addEventListener('click', function() {
        const ids = getSelectedBookIds();
        if (ids.length === 0) {
            showAlert('warning', 'No books selected.');
            return;
        }
        
        // Show bulk delete modal
        document.getElementById('bulkDeleteModal').classList.add('show');
        document.getElementById('bulkDeleteModal').style.display = 'block';
        
        // Confirm delete button handler
        document.getElementById('confirmBulkDeleteBtn').onclick = function() {
            fetch("{{ route('admin.books.bulkDelete') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ids})
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1200);
                } else if (data.status === 'partial') {
                    showAlert('warning', data.message + '<br>Failed IDs: ' + data.failed.join(', '));
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert('danger', data.message);
                }
            });
            
            // Hide modal
            document.getElementById('bulkDeleteModal').classList.remove('show');
            document.getElementById('bulkDeleteModal').style.display = 'none';
        };
        
        // Cancel delete button handler
        document.getElementById('cancelBulkDeleteBtn').onclick = function() {
            document.getElementById('bulkDeleteModal').classList.remove('show');
            document.getElementById('bulkDeleteModal').style.display = 'none';
        };
    });

    // Single delete modal handlers
    Array.from(document.getElementsByClassName('show-delete-modal')).forEach(btn => {
        btn.addEventListener('click', function() {
            const bookTitle = this.getAttribute('data-book-title');
            const deleteUrl = this.getAttribute('data-delete-url');
            document.getElementById('singleDeleteBookTitle').textContent = bookTitle;
            document.getElementById('singleDeleteForm').action = deleteUrl;
            document.getElementById('singleDeleteModal').classList.add('show');
            document.getElementById('singleDeleteModal').style.display = 'block';
        });
    });
    
    document.getElementById('cancelSingleDeleteBtn').onclick = function() {
        document.getElementById('singleDeleteModal').classList.remove('show');
        document.getElementById('singleDeleteModal').style.display = 'none';
    };
    
    // On page load, uncheck all checkboxes and update buttons
    const checkboxes = document.querySelectorAll('.select-book');
    checkboxes.forEach(cb => cb.checked = false);
    if (selectAll) selectAll.checked = false;
    updateBulkButtons();
});
</script>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-books"></th>
<th>Cover</th>
<th>Title</th>
<th>Author</th>
<th>Quantity</th>
<th>Status</th>
<th>Featured</th>
<th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
@php
    $activeLoans = $book->loans()->whereNull('returned_at')->count();
    $available = $book->quantity - $activeLoans;
    $coverUrl = $book->cover_image_url ?: ($book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/placeholder-book.jpg'));
@endphp
<tr @if($available < 1) style="background-color: #f8f9fa; color: #999;" @endif>
    <td><input type="checkbox" class="select-book" value="{{ $book->id }}"></td>
    <td><img src="{{ $coverUrl }}" alt="Cover" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;"></td>
    <td class="fw-medium">{{ $book->title }}</td>
    <td>{{ $book->author }}</td>
    <td>
        @if($available > 0)
            <span class="badge bg-success">{{ $available }} available</span>
        @else
            <span class="badge bg-secondary">Unavailable</span>
        @endif
    </td>
    <td>
        <span class="badge {{ $book->status === 'available' && $available > 0 ? 'bg-success' : 'bg-secondary' }}">
            {{ $book->status }}
        </span>
    </td>
                        <td>
                            @if($book->featured)
                                <span class="badge bg-primary">Featured</span>
                            @else
                                <span class="badge bg-light text-dark">No</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-sm btn-outline-secondary me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    <span class="visually-hidden">Edit</span>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger show-delete-modal" data-book-title="{{ $book->title }}" data-delete-url="{{ route('admin.books.destroy', $book) }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
    <span class="visually-hidden">Delete</span>
</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="pagination-container">
            {{ $books->links('pagination::bootstrap-4') }}
        </div>
        <style>
            .pagination-container .page-link {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
            .pagination-container .page-item .page-link {
                min-width: 30px;
                text-align: center;
            }
        </style>
    </div>
</div>

<!-- Single Delete Modal -->
<div class="modal fade" id="singleDeleteModal" tabindex="-1" aria-labelledby="singleDeleteModalLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="singleDeleteModalLabel">Delete Book</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="cancelSingleDeleteBtn"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete <span class="fw-bold" id="singleDeleteBookTitle"></span>?
      </div>
      <div class="modal-footer">
        <form method="POST" id="singleDeleteForm">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>
        <button type="button" class="btn btn-secondary" id="cancelSingleDeleteBtn">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="bulkDeleteModalLabel">Delete Selected Books</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="cancelBulkDeleteBtn"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the selected books?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="confirmBulkDeleteBtn">Yes, Delete Selected</button>
        <button type="button" class="btn btn-secondary" id="cancelBulkDeleteBtn">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Bulk Add Modal -->
<div class="modal fade" id="bulkAddModal" tabindex="-1" aria-labelledby="bulkAddModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkAddModalLabel">Bulk Add Books by ISBN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulk-add-form" action="{{ route('admin.books.bulkAdd') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="isbns" class="form-label">ISBN Numbers</label>
                        <textarea class="form-control" id="isbns" name="isbns" rows="5" placeholder="Enter multiple ISBNs separated by commas or new lines"></textarea>
                        <div class="form-text">Each book will be automatically synchronized with external source data.</div>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity for each book</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1">
                        <div class="form-text">Number of copies to add for each book.</div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured">
                        <label class="form-check-label" for="featured">
                            Mark all as featured books
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="bulk-add-submit">Add Books</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
