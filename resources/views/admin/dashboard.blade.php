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
                            <div class="card bg-success dashnum-card text-white overflow-hidden">
                                <span class="round small"></span>
                                <span class="round big"></span>
                                <div class="card-body">
                                    <span class="text-white d-block f-34 f-w-500 my-2">
                                    {{ $customers->where('status', 1)->count() }}
                                    <i class="fas fa-user-tie opacity-50"></i>
                                    </span>
                                    <p class="mb-0 opacity-50">Active Customers</p>
                                </div>
                            </div>
                        </div>

                        <!-- inactive customers -->
                        <div class="col-sm-3">
                            <div class="card bg-warning dashnum-card text-white overflow-hidden">
                                <span class="round small"></span>
                                <span class="round big"></span>
                                <div class="card-body">
                                    <span class="text-white d-block f-34 f-w-500 my-2">
                                    {{ $customers->where('status', 0)->count() }}
                                    <i class="fas fa-user-tie opacity-50"></i>
                                    </span>
                                    <p class="mb-0 opacity-50">In-active Customers</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(in_array(auth()->user()->role->slug, ['superadmin']))
                        <div class="col-sm-3">
                            <div class="card bg-primary-dark dashnum-card text-white overflow-hidden">
                                <span class="round small"></span>
                                <span class="round big"></span>
                                <div class="card-body">
                                    <span class="text-white d-block f-34 f-w-500 my-2">
                                    {{ $users->where('status', 1)->count() }}
                                    <i class="fas fa-user-tie opacity-50"></i>
                                    </span>
                                    <p class="mb-0 opacity-50">Users</p>
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