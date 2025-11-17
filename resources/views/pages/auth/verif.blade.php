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
                        <p class="mt-3 fs-15 fw-medium">Web Crafter Service &amp; Cirebon</p>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <div class="avatar-lg mx-auto">
                                    <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                                        <i class="ri-mail-line"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2 mt-4">
                                <div class="text-muted text-center mb-4 mx-lg-3">
                                    <h4>Verify Your Email</h4>
                                    @php
                                        $email = session('otp_user_email') ?? (session('otp_user_email') ?? 'example@abc.com');
                                    @endphp
                                    <p>Please enter the 6 digit code sent to <span
                                            class="fw-semibold">{{ $email }}</span></p>
                                </div>

                                {{-- alert area --}}
                                <div id="otp-alerts">
                                    @if (session('status'))
                                        <div class="alert alert-success">{{ session('status') }}</div>
                                    @endif
                                </div>

                                {{-- form (AJAX) --}}
                                <form id="otp-form" autocomplete="off" data-loader-ajax="true" data-loader-message="Verifying OTP...">
                                    @csrf
                                    <input type="hidden" name="otp" id="otp-hidden">

                                    <div class="row g-2 justify-content-center mb-3">
                                        @for ($i = 1; $i <= 6; $i++)
                                            <div class="col-2 col-sm-1">
                                                <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1"
                                                    autocomplete="off" class="form-control form-control-lg otp-digit"
                                                    id="digit{{ $i }}-input" data-index="{{ $i }}"
                                                    oninput="onDigitInput(event)" onkeydown="onDigitKeyDown(event)">
                                            </div>
                                        @endfor
                                    </div>

                                    <div class="d-grid">
                                        <button id="otp-submit" class="btn btn-success" type="submit">
                                            <span id="otp-submit-text">Confirm</span>
                                            <span id="otp-submit-spinner"
                                                class="spinner-border spinner-border-sm ms-2 d-none" role="status"
                                                aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </form>

                                <div class="mt-3 text-center">
                                    <button id="otp-resend" class="btn btn-link">Didn't receive a code? Resend</button>
                                </div>

                                <div class="mt-2 text-center">
                                    <a href="{{ route('login') }}"
                                        class="fw-semibold text-primary text-decoration-underline"> Back to login </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .otp-digit {
            height: 56px;
            border-radius: 8px;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 6px;
            background-color: #ffffff;
            color: #212529 !important;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            padding: 0;
        }

        @media (max-width: 576px) {
            .otp-digit {
                height: 48px;
                font-size: 20px;
                letter-spacing: 4px;
            }
        }

        .otp-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.1);
        }
    </style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/otp-verify.js') }}"></script>
@endpush
