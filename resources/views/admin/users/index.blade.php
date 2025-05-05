@extends('layouts.admin')
@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Users Management</h1>
        <p class="text-muted">View, edit, and manage users in the system.</p>
    </div>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="fw-medium">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-outline-info me-2 view-user-btn" data-id="{{ $user->id }}">View</button>
                                <button class="btn btn-sm btn-outline-warning me-2 edit-user-btn" data-id="{{ $user->id }}">Edit</button>
                                <button class="btn btn-sm btn-outline-danger delete-user-btn" data-id="{{ $user->id }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3">
        {{ $users->links() }}
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewUserModalBody">
        <!-- User info will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editUserForm">
        <div class="modal-body" id="editUserModalBody">
          <!-- Edit form will be loaded here -->
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Use Bootstrap 5 modal API
    const viewUserModal = new bootstrap.Modal(document.getElementById('viewUserModal'));
    const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));

    document.querySelectorAll('.view-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            fetch(`/admin/users/${userId}`)
                .then(r => r.json())
                .then(user => {
                    let html = `<ul class='list-group'>`;
                    html += `<li class='list-group-item'><strong>ID:</strong> ${user.id}</li>`;
                    html += `<li class='list-group-item'><strong>Name:</strong> ${user.name}</li>`;
                    html += `<li class='list-group-item'><strong>Email:</strong> ${user.email}</li>`;
                    html += `<li class='list-group-item'><strong>Role:</strong> ${user.role}</li>`;
                    html += `<li class='list-group-item'><strong>Registered:</strong> ${user.created_at}</li>`;
                    html += `</ul>`;
                    document.getElementById('viewUserModalBody').innerHTML = html;
                    viewUserModal.show();
                });
        });
    });

    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            fetch(`/admin/users/${userId}`)
                .then(r => r.json())
                .then(user => {
                    let html = `<input type='hidden' name='user_id' value='${user.id}'>`;
                    html += `<div class='mb-3'><label class='form-label'>Name</label><input type='text' name='name' class='form-control' value='${user.name}' required></div>`;
                    html += `<div class='mb-3'><label class='form-label'>Email</label><input type='email' name='email' class='form-control' value='${user.email}' required></div>`;
                    html += `<div class='mb-3'><label class='form-label'>Role</label><select name='role' class='form-select'><option value='user' ${user.role === 'user' ? 'selected' : ''}>User</option><option value='admin' ${user.role === 'admin' ? 'selected' : ''}>Admin</option></select></div>`;
                    document.getElementById('editUserModalBody').innerHTML = html;
                    editUserModal.show();
                    document.getElementById('editUserForm').onsubmit = function(e) {
                        e.preventDefault();
                        fetch(`/admin/users/${userId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: new URLSearchParams(new FormData(this)).toString()
                        }).then(resp => {
                            if (resp.ok) location.reload();
                            else alert('Error updating user.');
                        });
                    };
                });
        });
    });

    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to delete this user?')) return;
            const userId = this.getAttribute('data-id');
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(resp => {
                if (resp.ok) location.reload();
                else alert('Error deleting user.');
            });
        });
    });
});
</script>
@endsection
