@extends('layouts.admin-app')

@section('content')
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3>Dashboard</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex">
            <div class="w-100">
                <div class="row">
                    @if(in_array(auth()->user()->role->slug, ['superadmin', 'admin']))
                        <!-- active customers -->
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Active Customers</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-success">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">{{ $customers->where('status', 1)->count() }}</h1>
                                    <div class="mb-0">
                                        <a href="{{ route('admin.customers.index') }}">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- inactive customers -->
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">In-active Customers</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-danger">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">{{ $customers->where('status', 0)->count() }}</h1>
                                    <div class="mb-0">
                                        <a href="{{ route('admin.customers.index') }}">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(in_array(auth()->user()->role->slug, ['superadmin']))
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Users</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">{{ $users->where('status', 1)->count() }}</h1>
                                    <div class="mb-0">
                                        <a href="{{ route('admin.users.index') }}">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets') }}/js/pages/dashboard-default.js"></script>
@endpush