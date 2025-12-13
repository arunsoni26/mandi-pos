@extends('layouts.admin-app')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header text-white d-flex justify-content-between align-items-center rounded-top-4 p-3">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-people-fill me-2"></i> Users
            </h4>
            <button id="addUserBtn" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add User
            </button>
        </div>

        <div class="card-body">
            <!-- Filters Row -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <select id="filterStatus" class="form-select form-select-sm shadow-sm">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


<script>
    $(document).ready(function () {
        $('#filterStatus').select2({ theme: 'bootstrap-5', width: '100%' });

        let retryCount = 1;
        let table;

        function initUserTable(retries = retryCount) {
            if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().destroy();
            }

            table = $('#usersTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.users.list') }}",
                    data: function (d) {
                        d.status = $('#filterStatus').val(); // Apply the filter
                    },
                    error: function (xhr, error, thrown) {
                        console.error("DataTables AJAX error:", xhr.responseText);
                        let isServerError = false;

                        try {
                            const json = JSON.parse(xhr.responseText);
                            if (json.message && json.message === "Server Error") {
                                isServerError = true;
                            }
                        } catch (e) {
                            isServerError = xhr.status === 500;
                        }

                        if (retries > 0 && isServerError) {
                            console.warn(`Retrying table load... (${retryCount - retries + 1})`);
                            setTimeout(() => {
                                initUserTable(retries - 1);
                            }, 1000); // Retry after 1 second
                        } else {
                            alert("Failed to load user data. Please reload the page.");
                        }
                    }
                },
                columns: [
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'role' },
                    { data: 'status_toggle', orderable: false, searchable: false },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ]
            });
        }

        // Initial table load
        initUserTable();

        // Filters reload (will use existing table instance)
        $('#filterStatus').on('change keyup', function () {
            if (table) {
                table.ajax.reload();
            }
        });

        // Add Customer button
        $('#addUserBtn').on('click', function () {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('admin.users.form') }}",
                success: function (data) {
                    $('#addEditContent').html(data);
                    $('#editModal').modal('show');
                }
            });
        });

        // Edit user button (delegated)
        $(document).on('click', '.editUserBtn', function () {
            let id = $(this).data('id');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('admin.users.form') }}",
                data: {
                    userId: id
                },
                success: function (data) {
                    $('#addEditContent').html(data);
                    $('#editModal').modal('show');
                }
            });
        });

        // Toggle user status
        $(document).on('change', '.toggle-status', function () {
            $.post("{{ url('admin/users/toggle-status') }}/" + $(this).data('id'), {
                _token: "{{ csrf_token() }}"
            });
        });
    });
</script>


<style>
    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
    }
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: 4px 8px;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush
