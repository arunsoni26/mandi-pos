@extends('layouts.admin-app')

@section('content')

<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header text-white p-3">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-clock-history me-2"></i> Activity Logs
            </h4>
        </div>

        <div class="card-body">

            <!-- Filters -->
            <div class="row g-3 mb-3">

                <div class="col-md-3">
                    <input type="date" id="filterDate" class="form-control">
                </div>

                <div class="col-md-3">
                    <select id="filterUser" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="filterAction" class="form-select">
                        <option value="">All Actions</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                    </select>
                </div>

            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="activityTable" class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Reference</th>
                            <th>Old Values</th>
                            <th>New Values</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection

@push('custom-scripts')
    <script>
        $(document).ready(function() {

            let table = $('#activityTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('admin.activity.logs') }}",
                    data: function(d) {
                        d.date = $('#filterDate').val();
                        d.user_id = $('#filterUser').val();
                        d.action = $('#filterAction').val();
                    }
                },
                columns: [
                    { data: 'date' },
                    { data: 'user' },
                    { data: 'action' },
                    { data: 'model' },
                    { data: 'model_id' },
                    { data: 'old_values' },
                    { data: 'new_values' }
                ]
            });

            $('#filterDate, #filterUser, #filterAction').on('change', function() {
                table.ajax.reload();
            });

        });
    </script>
@endpush