@extends('layouts.auth')
@section('content')
    <div class="auth-page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">

                    <div class="card mt-4">
                        <div class="card-body p-4">

                            <div class="text-center mt-2">
                                <h5 class="text-primary">Forgot Password?</h5>
                                <p class="text-muted">Reset your password via email</p>

                                <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop"
                                    colors="primary:#0ab39c" class="avatar-xl"></lord-icon>
                            </div>

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
                                <form method="POST" action="{{ route('auth.forgot.send') }}" data-loader-message="Updating password...">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="Enter your email" required>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button class="btn btn-success w-100" type="submit">
                                            Send Reset Link
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="mb-0">
                            Wait, I remember my password...
                            <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">
                                Click here
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
