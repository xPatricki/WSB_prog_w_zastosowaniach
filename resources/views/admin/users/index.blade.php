@extends('layouts.admin')
@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Users Management</h1>
        <p class="text-muted">View, edit, and manage users in the system.</p>
    </div>
    <button class="btn btn-success" id="createUserBtn">Create User</button>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createUserForm">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
              <option value="user">User</option>
              <option value="admin">Admin</option>
              <option value="bookkeeper">BookKeeper</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th></th>
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
                        <td><img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=32" alt="avatar" class="rounded-circle" width="32" height="32"></td>
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
                                <button class="btn btn-sm btn-info me-2 view-user-btn" data-id="{{ $user->id }}" style="color: #fff;">View</button>
                                <button class="btn btn-sm btn-warning me-2 edit-user-btn" data-id="{{ $user->id }}" style="color: #fff;">Edit</button>
                                @if($user->id === Auth::id())
    <button class="btn btn-sm btn-danger" disabled style="opacity:0.5; cursor:not-allowed; color: #fff;">Delete</button>
@else
    <button class="btn btn-sm btn-danger delete-user-btn" data-id="{{ $user->id }}" style="color: #fff;">Delete</button>
@endif
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
    // Create User Modal logic
    const createUserModal = new bootstrap.Modal(document.getElementById('createUserModal'));
    document.getElementById('createUserBtn').addEventListener('click', function() {
        document.getElementById('createUserForm').reset();
        createUserModal.show();
    });
    document.getElementById('createUserForm').onsubmit = function(e) {
        e.preventDefault();
        fetch('/admin/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: new URLSearchParams(new FormData(this)).toString()
        }).then(resp => {
            if (resp.ok) location.reload();
            else alert('Error creating user.');
        });
    };

    // Use Bootstrap 5 modal API
    const viewUserModal = new bootstrap.Modal(document.getElementById('viewUserModal'));
    const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));

    document.querySelectorAll('.view-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            fetch(`/admin/users/${userId}`)
                .then(r => r.json())
                .then(user => {
                    let html = `<div class='text-center mb-3'><img src='https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0D8ABC&color=fff&size=64' alt='avatar' class='rounded-circle'></div>`;
                    html += `<ul class='list-group'>`;
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
                    let isSelf = user.id == {{ Auth::id() }};
html += `<div class='mb-3'><label class='form-label'>Role</label><select name='role' class='form-select' ${isSelf ? 'disabled' : ''}><option value='user' ${user.role === 'user' ? 'selected' : ''}>User</option><option value='admin' ${user.role === 'admin' ? 'selected' : ''}>Admin</option><option value='bookkeeper' ${user.role === 'bookkeeper' ? 'selected' : ''}>BookKeeper</option></select></div>`;
                    document.getElementById('editUserModalBody').innerHTML = html;
                    editUserModal.show();
                    document.getElementById('editUserForm').onsubmit = function(e) {
                        e.preventDefault();
                        let formData = new FormData(this);
formData.append('_method', 'PUT');
fetch(`/admin/users/${userId}`, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: formData
}).then(resp => {
                            if (resp.ok) location.reload();
                            else alert('Error updating user.');
                        });
                    };
                });
        });
    });

    // Custom Delete Confirmation Modal
    let deleteUserId = null;
    const deleteUserModal = document.createElement('div');
    deleteUserModal.innerHTML = `
      <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" id="confirmDeleteUserBtn">Delete</button>
            </div>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(deleteUserModal);
    const deleteUserModalInstance = new bootstrap.Modal(document.getElementById('deleteUserModal'));

    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteUserId = this.getAttribute('data-id');
            deleteUserModalInstance.show();
        });
    });
    document.getElementById('confirmDeleteUserBtn').onclick = function() {
        if (!deleteUserId) return;
        fetch(`/admin/users/${deleteUserId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(resp => {
            if (resp.ok) location.reload();
            else alert('Error deleting user.');
        });
        deleteUserModalInstance.hide();
        deleteUserId = null;
    };

});
</script>
@endsection
