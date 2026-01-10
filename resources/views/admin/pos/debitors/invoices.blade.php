@extends('layouts.admin-app')

@section('content')

<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="customersTab">
        <div class="container-fluid py-4">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header text-white d-flex justify-content-between align-items-center rounded-top-4 p-3">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-people-fill me-2"></i> Invoices
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Filters Row -->
                    <div class="row g-3 mb-3">
                        <form method="GET">
                            <input
                                type="date"
                                name="date"
                                value="{{ $date }}"
                                class="form-control"
                                onchange="this.form.submit()"
                            >
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="customersTable" class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <tr>
                                        <th width="20%">Invoice ID</th>
                                        <th width="20%">Invoice Date</th>
                                        <th width="40%">Debitor</th>
                                        <th width="20%">Action</th>
                                    </tr>
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
        $('#filterStatus').select2({ theme: 'bootstrap-5', width: '100%' });
        
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
                    url: "{{ route('admin.pos.debitors.invoices') }}",
                    data: function(d) {
                        d.date = $('input[name="date"]').val(); // ✅ SEND DATE
                        d.status = $('#filterStatus').val();
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
                    { data: 'invoice' },
                    { data: 'invoice_date' },
                    { data: 'debitor_name' },
                    { data: 'actions', orderable: false, searchable: false }
                ]
            });
        }

        // Initialize customers table on load
        initCustomersTable();

        // Reload on filters
        $('#filterStatus, #filterCode').on('change keyup', function() {
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
