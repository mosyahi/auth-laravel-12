@extends('layouts.dashboard')
@section('content')
    <div class="container-fluid">

        {{-- ROW TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Blog</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Blog</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- ROW BARU: PERMISSION LIST + AKSES USER --}}
        {{-- ========================================= --}}
        <div class="row mt-3">

            {{-- CARD 1: Semua daftar permission --}}
            <div class="col-md-6">
                <div class="card border shadow-sm mb-4">
                    <div class="card-header bg-primary text-white fw-semibold d-flex align-items-center">
                        <i class="ri-shield-user-line me-2"></i> Semua Daftar Permission (Global)
                    </div>
                    <div class="card-body">
                        @php
                            use Spatie\Permission\Models\Permission;
                            $allPermissions = Permission::orderBy('name')->get();
                        @endphp

                        @if ($allPermissions->count() > 0)
                            @foreach ($allPermissions as $perm)
                                <span class="badge bg-primary-subtle text-primary badge-border mb-1 me-1" style="font-size: 0.85rem;">
                                    {{ $perm->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-danger">Belum ada permission dibuat.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CARD 2: Detail hak akses user --}}
            <div class="col-md-6">
                <div class="card border shadow-sm mb-4">
                    <div class="card-header bg-success text-white fw-semibold d-flex align-items-center">
                        <i class="ri-user-settings-line me-2"></i> Hak Akses Anda
                    </div>
                    <div class="card-body">

                        {{-- Nama --}}
                        <p class="fw-bold mb-1">Nama User:</p>
                        <p class="text-muted">{{ auth()->user()->name }}</p>

                        {{-- Role --}}
                        <p class="fw-bold mb-1">Role Anda:</p>
                        <p>
                            @forelse(auth()->user()->getRoleNames() as $role)
                                <span class="badge bg-dark-subtle text-dark badge-border me-1 mb-1">{{ $role }}</span>
                            @empty
                                <span class="badge bg-danger-subtle text-danger badge-border">Tidak punya role</span>
                            @endforelse
                        </p>

                        {{-- Permission User --}}
                        <p class="fw-bold mb-1">Permission yang Anda Miliki:</p>
                        <p>
                            @forelse(auth()->user()->getAllPermissions() as $perm)
                                <span class="badge bg-success-subtle text-success badge-border me-1 mb-1">{{ $perm->name }}</span>
                            @empty
                                <span class="badge bg-danger-subtle text-danger badge-border">Tidak punya permission</span>
                            @endforelse
                        </p>

                        {{-- Permission yang tidak dimiliki --}}
                        <p class="fw-bold mb-1">Permission yang Tidak Anda Miliki:</p>
                        <p>
                            @php
                                $userPerm = auth()->user()->getAllPermissions()->pluck('name')->toArray();
                            @endphp

                            @foreach ($allPermissions as $perm)
                                @if (!in_array($perm->name, $userPerm))
                                    <span class="badge bg-danger-subtle text-danger badge-border me-1 mb-1">
                                        {{ $perm->name }}
                                    </span>
                                @endif
                            @endforeach
                        </p>

                    </div>
                </div>
            </div>

        </div> {{-- END ROW --}}
    </div>
@endsection
