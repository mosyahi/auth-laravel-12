@extends('layouts.auth')

@section('content')
    <div class="auth-page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">

                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-primary">Create new password</h5>
                                <p class="text-muted">Pastikan password anda mengandung huruf besar, kecil, angka dan symbol.
                                </p>
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

                            <div class="p-2 mt-3">
                                <form id="reset-form" method="POST" action="{{ route('auth.reset.update') }}"
                                    data-loader-ajax="true" data-loader-message="Verifying and updating...">
                                    @csrf

                                    {{-- token dari url --}}
                                    <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input id="email" name="email" type="email"
                                            value="{{ old('email') ?? request('email') }}"
                                            class="form-control @error('email') is-invalid @enderror" required>
                                        {{-- @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror --}}
                                    </div>

                                    <div class="mb-3">
                                        <label for="password-input" class="form-label">Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input id="password-input" name="password" type="password"
                                                class="form-control pe-0" placeholder="Enter new password"
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
                                                class="form-control pe-0" placeholder="Confirm new password"
                                                onpaste="return false" minlength="8" required>
                                            <button type="button" class="btn btn-soft-dark password-toggle"
                                                data-target="#password-confirm">
                                                <i class="ri-eye-fill align-middle"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button id="reset-submit" type="submit" class="btn btn-success" disabled>
                                            Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="mb-0">Wait, I remember my password... <a href="{{ route('login') }}"
                                class="fw-semibold text-primary text-decoration-underline"> Click here </a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pwd = document.getElementById('password-input');
            const pwdConfirm = document.getElementById('password-confirm');
            const submitBtn = document.getElementById('reset-submit');

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

            const form = document.getElementById('reset-form');
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
                    submitBtn.innerText = 'Updating...';
                });
            }

            updatePasswordFeedback(pwd.value || '');
            checkFormValidity();
        });
    </script>
@endpush
