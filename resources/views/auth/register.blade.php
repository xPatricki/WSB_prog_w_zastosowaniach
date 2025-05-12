@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Register</div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="position-relative">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" minlength="8">
                            <button type="button" class="btn btn-sm position-absolute end-0 top-0 mt-1 me-1 password-toggle" style="display: none; background: transparent; border: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-1">
                            <div class="progress" style="height: 5px;">
                                <div id="password-strength" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div id="password-feedback" class="form-text mt-1"></div>
                        <div class="form-text mt-1">
                            Password must contain at least:
                            <ul class="mb-0 ps-3 mt-1">
                                <li id="length-check"><span class="text-muted">8 characters</span></li>
                                <li id="uppercase-check"><span class="text-muted">One uppercase letter</span></li>
                                <li id="number-check"><span class="text-muted">One number</span></li>
                                <li id="special-check"><span class="text-muted">One special character</span></li>
                            </ul>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">Confirm Password</label>
                        <div class="position-relative">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            <button type="button" class="btn btn-sm position-absolute end-0 top-0 mt-1 me-1 password-toggle" style="display: none; background: transparent; border: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Register
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('password-strength');
    const passwordFeedback = document.getElementById('password-feedback');
    const confirmInput = document.getElementById('password-confirm');
    
    // Password visibility toggle functionality
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const parentContainer = this.closest('.position-relative');
            const input = parentContainer.querySelector('input[type="password"], input[type="text"]');
            const type = input.getAttribute('type');
            const eyeIcon = this.querySelector('svg');
            
            if (type === 'password') {
                input.setAttribute('type', 'text');
                // Change to eye-slash icon
                eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-eye-slash" viewBox="0 0 16 16">
                      <path fill="#000000" d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                      <path fill="#000000" d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                      <path fill="#000000" d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5c-3 0-6-1.5-7.5-3.5.5-.5 1-1 1.5-1.5 1-1 2-1.5 3-2l.78.78z"/>
                      <path fill="#000000" d="M5.354 7.146l.896.897.896-.897a.5.5 0 1 1 .707.708l-.896.896.896.897a.5.5 0 0 1-.707.707l-.896-.897-.897.897a.5.5 0 0 1-.707-.707l.897-.897-.897-.896a.5.5 0 1 1 .707-.708l.897.897z"/>
                    </svg>
                `;
            } else {
                input.setAttribute('type', 'password');
                // Change back to eye icon
                eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-eye" viewBox="0 0 16 16">
                      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    </svg>
                `;
            }
        });
    });
    
    // Show/hide toggle buttons based on input content
    function togglePasswordButton(input) {
        // Find the toggle button within the parent container
        const parentContainer = input.closest('.position-relative');
        const button = parentContainer.querySelector('.password-toggle');
        
        if (input.value) {
            button.style.display = 'block';
        } else {
            button.style.display = 'none';
        }
    }
    
    passwordInput.addEventListener('input', function() {
        togglePasswordButton(this);
    });
    
    confirmInput.addEventListener('input', function() {
        togglePasswordButton(this);
    });
    
    // Validation criteria elements
    const lengthCheck = document.getElementById('length-check');
    const uppercaseCheck = document.getElementById('uppercase-check');
    const numberCheck = document.getElementById('number-check');
    const specialCheck = document.getElementById('special-check');
    
    // Validation patterns
    const patterns = {
        length: /.{8,}/,
        uppercase: /[A-Z]/,
        number: /[0-9]/,
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/
    };
    
    // Update function for password validation
    function updatePasswordStrength() {
        const password = passwordInput.value;
        let strength = 0;
        let feedback = '';
        
        // Check each criteria and update UI
        const meetsLength = patterns.length.test(password);
        const meetsUppercase = patterns.uppercase.test(password);
        const meetsNumber = patterns.number.test(password);
        const meetsSpecial = patterns.special.test(password);
        
        // Update check marks and calculate strength
        updateCheckElement(lengthCheck, meetsLength);
        updateCheckElement(uppercaseCheck, meetsUppercase);
        updateCheckElement(numberCheck, meetsNumber);
        updateCheckElement(specialCheck, meetsSpecial);
        
        // Calculate strength (25% for each criteria met)
        if (meetsLength) strength += 25;
        if (meetsUppercase) strength += 25;
        if (meetsNumber) strength += 25;
        if (meetsSpecial) strength += 25;
        
        // Update progress bar
        passwordStrength.style.width = strength + '%';
        passwordStrength.setAttribute('aria-valuenow', strength);
        
        // Set color based on strength
        if (strength < 50) {
            passwordStrength.className = 'progress-bar bg-danger';
            feedback = 'Weak password';
        } else if (strength < 100) {
            passwordStrength.className = 'progress-bar bg-warning';
            feedback = 'Moderate password';
        } else {
            passwordStrength.className = 'progress-bar bg-success';
            feedback = 'Strong password';
        }
        
        passwordFeedback.textContent = feedback;
    }
    
    // Helper function to update check elements
    function updateCheckElement(element, isValid) {
        const span = element.querySelector('span');
        const originalText = span.textContent.replace(/✓\s*/g, '').trim();
        
        if (isValid) {
            span.className = 'text-success';
            span.textContent = '✓ ' + originalText;
        } else {
            span.className = 'text-muted';
            span.textContent = originalText;
        }
    }
    
    // Event listeners
    passwordInput.addEventListener('input', updatePasswordStrength);
    
    // Disable form submission if password is not valid
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const allValid = patterns.length.test(password) && 
                         patterns.uppercase.test(password) && 
                         patterns.number.test(password) && 
                         patterns.special.test(password);
        
        if (!allValid) {
            e.preventDefault();
            passwordInput.setCustomValidity('Password does not meet all requirements');
            passwordInput.reportValidity();
        } else {
            passwordInput.setCustomValidity('');
        }
    });
});
</script>
@endpush
