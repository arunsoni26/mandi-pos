@extends('layouts.admin-app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-white">Roles</h4>
            <i class="bi bi-shield-lock"></i>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        @forelse($roles as $role)
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 d-flex justify-content-center add-permissions-btn" data-role-id="{{ $role->id }}">
            
            <div class="role-circle-card text-center shadow-sm">
                <div class="role-circle-content">

                    <!-- Icon -->
                    <div class="role-circle-icon m-3">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                    <!-- Role Name -->
                    <h6 class="fw-bold text-capitalize">
                        {{ $role->name }}
                    </h6>

                    <!-- Button -->
                    <!-- <button 
                        class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-key-fill"></i>
                    </button> -->

                </div>
            </div>

        </div>
        @empty
        <div class="col-12 text-center">
            <div class="alert alert-warning rounded-pill py-4">
                <i class="bi bi-exclamation-circle-fill"></i>
                No roles found
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('custom-css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .role-circle-card {
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffffff, #f1f3f5);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.35s ease;
        cursor: pointer;
    }

    .role-circle-card:hover {
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }

    .role-circle-content {
        text-align: center;
        padding: 20px;
    }

    .role-circle-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(13, 110, 253, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 34px;
        color: #0d6efd;
    }

    .role-circle-card button {
        border-radius: 50px;
        padding: 5px 14px;
    }
</style>
@endpush

@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    $('.add-permissions-btn').on('click', function(){
        let roleId = $(this).data('role-id');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{route('admin.role-permission-form')}}",
            data: {
                roleId: roleId
            },
            success: function (data) {
                $('#addEditContent').html(data);
                $('#editModal').modal('show');
            }
        });
    });
});
</script>
@endpush
