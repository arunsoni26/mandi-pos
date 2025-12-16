@extends('layouts.admin-app')

@section('content')
<ul class="nav nav-tabs" id="customerTabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#customersTab">Customers</a>
    </li>
</ul>

<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="customersTab">
        <div class="container-fluid py-4">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header text-white d-flex justify-content-between align-items-center rounded-top-4 p-3">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-people-fill me-2"></i> Customers
                    </h4>
                    <button id="addCustomerBtn" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Customer
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
                        <div class="col-md-3">
                            <input type="text" id="filterCode" placeholder="Search Code..." class="form-control shadow-sm" />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="customersTable" class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>PAN</th>
                                    <th>Father's Name</th>
                                    <th>Client Type</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th>Dashboard</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#filterGroup, #filterStatus').select2({ theme: 'bootstrap-5', width: '100%' });
        
        let retryCount = 1;
        let table;

        function initCustomersTable(retries = retryCount) {
            if ($.fn.DataTable.isDataTable('#customersTable')) {
                $('#customersTable').DataTable().destroy();
            }

            table = $('#customersTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.customers.list') }}",
                    data: function(d) {
                        d.group_id = $('#filterGroup').val();
                        d.status = $('#filterStatus').val();
                        d.code = $('#filterCode').val();
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
                            console.warn(`Retrying customer table load... (${retryCount - retries + 1})`);
                            setTimeout(() => {
                                initCustomersTable(retries - 1);
                            }, 1000);
                        } else {
                            alert("Failed to load customer data. Please reload the page.");
                        }
                    }
                },
                columns: [
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'pan' },
                    { data: 'fathers_name' },
                    { data: 'client_type_status' },
                    { data: 'group' },
                    { data: 'status_toggle', orderable: false, searchable: false },
                    { data: 'dashboard_toggle', orderable: false, searchable: false },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ]
            });
        }

        // Initialize customers table on load
        initCustomersTable();

        // Reload on filters
        $('#filterGroup, #filterStatus, #filterCode').on('change keyup', function() {
            if (table) {
                table.ajax.reload();
            }
        });

    });

    // Add Customer button
    $('#addCustomerBtn').on('click', function(){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{route('admin.customers.form')}}",
            success: function (data) {
                $('#addEditContent').html(data);
                $('#editModal').modal('show');
            }
        });
    });

    // Edit Customer button (delegated)
    $(document).on('click', '.editCustomerBtn', function(){
        let id = $(this).data('id');
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{route('admin.customers.form')}}",
            data: {
                customerId: id
            },
            success: function (data) {
                $('#addEditContent').html(data);
                $('#editModal').modal('show');
            }
        });
    });
    

    $(document).on('change', '.toggle-status', function(){
        $.post("{{ url('admin/customers/toggle-status') }}/" + $(this).data('id'), {_token: "{{ csrf_token() }}"});
    });

    $(document).on('change', '.toggle-dashboard', function(){
        $.post("{{ url('admin/customers/toggle-dashboard') }}/" + $(this).data('id'), {_token: "{{ csrf_token() }}"});
    });

    function loadGroups(){
        $.get('{{ route("admin.customers.groups.list") }}', function(data){
            let html = '';
            data.forEach(group => {
                var createdAt = moment(group.created_at);
                html += `<tr>
                            <td>${group.name}</td>
                            <td>${createdAt.format('MMMM D, YYYY')}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-primary btn-sm" onclick="openGroupForm(${group.id})">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteGroup(${group.id})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
            });
            $('#groupsTable tbody').html(html);
        });
    }

    loadGroups();

    function openGroupForm(id = null) {
        $.post('{{ route("admin.customers.groups.form") }}', { id: id, _token: '{{ csrf_token() }}' }, function(html){
            $('#addEditContent').html(html);
            $('#editModal').modal('show');
        });
    }


    function deleteGroup(groupId){
        if(confirm('Are you sure?')){
            $.ajax({
                url: '{{ route("admin.customers.groups.delete") }}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    'groupId': groupId
                },
                success: function(res){
                    if(res.success){
                        loadGroups();
                        toastr.success(res.message);
                    }
                }
            });
        }
    }

    $(document).on('click', '.viewCustomer', function () {
        let id = $(this).data('id');
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{route('admin.customers.view')}}",
            data: {
                custId: id
            },
            success: function (data) {
                $('#addEditContent').html(data);
                $('#editModal').modal('show');
            }
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
