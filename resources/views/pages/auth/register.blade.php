@extends('layouts.auth')
@section('content')
    <div class="auth-page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mt-sm-5 mb-4 text-white-50">
                        <div>
                            <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                                <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="20">
                            </a>
                        </div>
                        <p class="mt-3 fs-15 fw-medium">Premium Admin & Dashboard Template</p>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">

                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-primary">Create New Account</h5>
                                <p class="text-muted">Get your free velzon account now</p>
                            </div>

                            {{-- status / errors --}}
                            @if (session('status'))
                                <div class="alert alert-success mt-3">{{ session('status') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger mt-3">
                                    <ul class="m-0">
                                        @foreach ($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="p-2 mt-4">
                                <form id="register-form" method="POST" action="{{ route('auth.register.post') }}"
                                    class="needs-validation" novalidate data-loader-ajax="true"
                                    data-loader-message="Membuat akun...">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Enter email address" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name <span
                                                class="text-danger">*</span></label>
                                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Enter name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password-input" class="form-label">Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input id="password-input" name="password" type="password"
                                                class="form-control pe-0" placeholder="Enter password"
                                                onpaste="return false" minlength="8" required>
                                            <button type="button" class="btn btn-soft-primary password-toggle"
                                                data-target="#password-input">
                                                <i class="ri-eye-fill align-middle"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password-confirm" class="form-label">Confirm Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input id="password-confirm" name="password_confirmation" type="password"
                                                class="form-control pe-0" placeholder="Confirm password"
                                                onpaste="return false" minlength="8" required>
                                            <button type="button" class="btn btn-soft-dark password-toggle"
                                                data-target="#password-confirm">
                                                <i class="ri-eye-fill align-middle"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" name="otp_type" value="Y">

                                    <div class="mb-4">
                                        <p class="mb-0 fs-12 text-muted fst-italic">By registering you agree to the Velzon
                                            <a href="#"
                                                class="text-primary text-decoration-underline fst-normal fw-medium">Terms of
                                                Use</a>
                                        </p>
                                    </div>

                                    <div class="d-grid">
                                        <button id="register-submit" class="btn btn-success w-100" type="submit" disabled>
                                            Sign Up
                                        </button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <div class="signin-other-title">
                                            <h5 class="fs-13 mb-4 title text-muted">Create account with</h5>
                                        </div>

                                        <div>
                                            <button type="button"
                                                class="btn btn-primary btn-icon waves-effect waves-light"><i
                                                    class="ri-facebook-fill fs-16"></i></button>
                                            <button type="button"
                                                class="btn btn-danger btn-icon waves-effect waves-light"><i
                                                    class="ri-google-fill fs-16"></i></button>
                                            <button type="button"
                                                class="btn btn-dark btn-icon waves-effect waves-light"><i
                                                    class="ri-github-fill fs-16"></i></button>
                                            <button type="button"
                                                class="btn btn-info btn-icon waves-effect waves-light"><i
                                                    class="ri-twitter-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                    <div class="mt-4 text-center">
                        <p class="mb-0">Already have an account ? <a href="{{ route('login') }}"
                                class="fw-semibold text-primary text-decoration-underline"> Signin </a> </p>
                    </div>

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pwd = document.getElementById('password-input');
            const pwdConfirm = document.getElementById('password-confirm');
            const submitBtn = document.getElementById('register-submit');

            document.querySelectorAll('.password-toggle').forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = document.querySelector(btn.getAttribute('data-target'));
                    if (!target) return;
                    if (target.type === 'password') {
                        target.type = 'text';
                        btn.querySelector('i')?.classList.remove('ri-eye-fill');
                        btn.querySelector('i')?.classList.add('ri-eye-off-fill');
                    } else {
                        target.type = 'password';
                        btn.querySelector('i')?.classList.remove('ri-eye-off-fill');
                        btn.querySelector('i')?.classList.add('ri-eye-fill');
                    }
                    target.focus();
                });
            });

            function hasMinLength(v) {
                return v.length >= 8;
            }

            function hasLower(v) {
                return /[a-z]/.test(v);
            }

            function hasUpper(v) {
                return /[A-Z]/.test(v);
            }

            function hasNumber(v) {
                return /[0-9]/.test(v);
            }

            function hasSymbol(v) {
                return /[^A-Za-z0-9]/.test(v);
            }

            function updatePasswordFeedback(value) {
                const parent = pwd.parentNode;
                if (!parent) return;

                let feedback = parent.querySelector('.password-feedback');
                const unmet = [];

                if (!hasMinLength(value)) unmet.push('Minimal 8 karakter.');
                if (!hasLower(value)) unmet.push('Harus mengandung huruf kecil (a–z).');
                if (!hasUpper(value)) unmet.push('Harus mengandung huruf besar (A–Z).');
                if (!hasNumber(value)) unmet.push('Harus mengandung angka (0–9).');
                if (!hasSymbol(value)) unmet.push('Harus mengandung simbol (mis. !@#$%^&*).');

                if (unmet.length === 0) {
                    pwd.classList.remove('is-invalid');
                    pwd.classList.add('is-valid');
                    if (feedback) feedback.remove();
                } else {
                    pwd.classList.remove('is-valid');
                    pwd.classList.add('is-invalid');

                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback password-feedback';
                        parent.appendChild(feedback);
                    }

                    let html = '<div><strong>Perbaiki password:</strong><ul class="mb-0">';
                    unmet.forEach(rule => {
                        html += '<li>' + rule + '</li>';
                    });
                    html += '</ul></div>';
                    feedback.innerHTML = html;
                }

                return unmet.length === 0;
            }

            function setConfirmFeedback(show) {
                const parent = pwdConfirm.parentNode;
                if (!parent) return;
                let feedback = parent.querySelector('.invalid-feedback.confirm-feedback');

                if (show) {
                    pwdConfirm.classList.add('is-invalid');
                    pwdConfirm.classList.remove('is-valid');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback confirm-feedback';
                        feedback.innerText = 'Password confirmation does not match.';
                        parent.appendChild(feedback);
                    } else {
                        feedback.innerText = 'Password confirmation does not match.';
                    }
                } else {
                    if (feedback) feedback.remove();
                    pwdConfirm.classList.remove('is-invalid');
                    if (pwdConfirm.value.length && pwdConfirm.value === pwd.value) {
                        pwdConfirm.classList.add('is-valid');
                    } else {
                        pwdConfirm.classList.remove('is-valid');
                    }
                }
            }

            function checkFormValidity() {
                const v = pwd.value || '';
                const confirm = pwdConfirm.value || '';

                const allValid = updatePasswordFeedback(v);
                const confirmOk = (v === confirm) && v.length > 0;

                submitBtn.disabled = !(allValid && confirmOk);

                if (confirm.length && !confirmOk) {
                    setConfirmFeedback(true);
                } else {
                    setConfirmFeedback(false);
                }
            }

            if (pwd) {
                pwd.addEventListener('input', () => {
                    updatePasswordFeedback(pwd.value);
                    checkFormValidity();
                });
                pwd.addEventListener('paste', e => e.preventDefault());
            }
            if (pwdConfirm) {
                pwdConfirm.addEventListener('input', checkFormValidity);
                pwdConfirm.addEventListener('paste', e => e.preventDefault());
            }

            const form = document.getElementById('register-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const v = pwd.value || '';
                    if (!(hasMinLength(v) && hasLower(v) && hasUpper(v) && hasNumber(v) && hasSymbol(v) &&
                            v === pwdConfirm.value)) {
                        e.preventDefault();
                        checkFormValidity();
                        return;
                    }
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Creating...';
                });
            }

            updatePasswordFeedback(pwd.value || '');
            checkFormValidity();
        });
    </script>
@endpush
