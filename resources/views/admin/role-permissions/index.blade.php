@extends('layouts.admin-app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Roles</h4>
            <i class="bi bi-shield-lock"></i>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Role Name</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td class="fw-semibold">{{ ucfirst($role->name) }}</td>
                            <td class="text-center">
                                <button class="btn btn-outline-primary btn-sm add-permissions-btn" 
                                    data-role-id="{{ $role->id }}">
                                    <i class="bi bi-key"></i> Add Permissions
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @if($roles->isEmpty())
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">
                                <i class="bi bi-exclamation-circle"></i> No roles found
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Bootstrap Icons CDN (optional) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
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
