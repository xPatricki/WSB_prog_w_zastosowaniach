<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Library App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .book-cover {
            height: 200px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .countdown {
            font-weight: bold;
        }
        .overdue {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                Library App
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('books.index') ? 'active' : '' }}" href="{{ route('books.index') }}">Browse Books</a>
                    </li>
                    @auth
                        @if(strtolower(auth()->user()->role) === 'user')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('loans.index') ? 'active' : '' }}" href="{{ route('loans.index') }}">My Books</a>
                            </li>
                        @endif
                        @if(strtolower(auth()->user()->role) === 'admin' || strtolower(auth()->user()->role) === 'bookkeeper')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin</a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8ABC&color=fff&size=32" alt="avatar" class="rounded-circle me-2" width="32" height="32">
    {{ auth()->user()->name }}
</a>
                            <ul class="dropdown-menu dropdown-menu-end">
    <li><a class="dropdown-item" href="#" id="openProfileModal">Profile</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <style>
    html, body { height: 100%; }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    main.min-vh-100 { flex: 1 0 auto; }
</style>
<footer class="py-3 border-top" style="margin-top:auto;">
    <div class="container text-center">
        <p class="text-muted mb-0">
            &copy; {{ date('Y') }} Library App. All rights reserved.<br>
            Authors: Dawid Skrzypacz, Patryk Pawlicki, Witold Mikołajczak
        </p>
    </div>
</footer>

@auth
<!-- Profile Modal - Only visible to logged in users -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profileModalLabel">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="profileForm">
        <div class="modal-body">
          <div id="profileFormAlert"></div>
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
          </div>
          <hr>
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" autocomplete="new-password">
          </div>
          <div class="mb-3">
            <label class="form-label">Repeat New Password</label>
            <input type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endauth

<script>
document.addEventListener('DOMContentLoaded', function() {
    @auth
    const profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
    const openProfileModalBtn = document.getElementById('openProfileModal');
    
    if (openProfileModalBtn) {
        openProfileModalBtn.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('profileForm').reset();
        document.getElementById('profileFormAlert').innerHTML = '';
        profileModal.show();
    });
    }
    
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.onsubmit = function(e) {
        e.preventDefault();
        const form = this;
        fetch('/profile', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: new FormData(form)
        }).then(async resp => {
            const data = await resp.json();
            if (resp.ok && data.success) {
                document.getElementById('profileFormAlert').innerHTML = '<div class="alert alert-success">Profile updated successfully.</div>';
                setTimeout(() => location.reload(), 1200);
            } else {
                let msg = data.message || 'Error updating profile.';
                if (data.errors) {
                    msg += '<ul>' + Object.values(data.errors).map(e => `<li>${e}</li>`).join('') + '</ul>';
                }
                document.getElementById('profileFormAlert').innerHTML = `<div class="alert alert-danger">${msg}</div>`;
            }
        }).catch(() => {
            document.getElementById('profileFormAlert').innerHTML = '<div class="alert alert-danger">Error updating profile.</div>';
        });
    };
    }
    @endauth
});
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Simple countdown timer for due dates
            const countdownElements = document.querySelectorAll('.countdown-timer');
            countdownElements.forEach(element => {
                const dueDate = new Date(element.dataset.dueDate);
                
                function updateCountdown() {
                    const now = new Date();
                    const diff = dueDate - now;
                    
                    if (diff <= 0) {
                        element.innerHTML = '<span class="overdue">Overdue</span>';
                        return;
                    }
                    
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    
                    element.innerHTML = `${days} days, ${hours} hours`;
                }
                
                updateCountdown();
                setInterval(updateCountdown, 60000); // Update every minute
            });
        });
    </script>
</body>
</html>

