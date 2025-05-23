@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5 class="text-center mb-3">Demo Logins</h5>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <strong>Admin</strong><br>
                            <small>Email:</small> <code>admin@admin.com</code> <br>
                            <small>Password:</small> <code>admin</code>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary select-demo" data-email="admin@admin.com" data-password="admin">Select</button>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <strong>Regular User</strong><br>
                            <small>Email:</small> <code>user@example.com</code> <br>
                            <small>Password:</small> <code>password</code>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary select-demo" data-email="user@example.com" data-password="password">Select</button>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <strong>BookKeeper</strong><br>
                            <small>Email:</small> <code>bookkeeper@example.com</code> <br>
                            <small>Password:</small> <code>password</code>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary select-demo" data-email="bookkeeper@example.com" data-password="password">Select</button>
                            </div>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember Me
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Login
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all select-demo buttons
        const selectButtons = document.querySelectorAll('.select-demo');
        
        selectButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Get the email and password from data attributes
                const email = this.getAttribute('data-email');
                const password = this.getAttribute('data-password');
                
                // Fill the form fields
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
                
                // Optional: Add visual feedback
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-primary');
                setTimeout(() => {
                    button.classList.add('btn-outline-primary');
                    button.classList.remove('btn-success');
                }, 1000);
            });
        });
    });
</script>
@endpush
