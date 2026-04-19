@extends('layouts.admin-app')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header text-white rounded-top-4 p-3 bg-gradient-primary">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Debtor Wise Report
            </h4>
        </div>

        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <input type="text" id="dateRange" class="form-control" placeholder="Select Date Range">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100" id="filterBtn">Filter</button>
                </div>

                <div class="col-md-2">
                    <a href="#" id="exportCsvBtn" class="btn btn-success w-100">
                        Download CSV
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="debtorReportTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Debtor Name</th>
                            <th>Total Pieces</th>
                            <th>Total Invoice Amount</th>
                        </tr>
                    </thead>

                    <tfoot class="table-light">
                        <tr>
                            <th>Grand Total</th>
                            <th id="footerPieces"></th>
                            <th id="footerAmount"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$(document).ready(function() {
    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d"
    });

    let table = $('#debtorReportTable').DataTable({
        processing: true,
        ajax: {
            url: "{{ route('admin.reports.debtor-wise') }}",
            data: function(d) {
                d.date_range = $('#dateRange').val();
            },
            dataSrc: function(json) {
                $('#footerPieces').html(json.grandTotals.total_pieces);
                $('#footerAmount').html('₹ ' + json.grandTotals.total_amount);
                return json.data;
            }
        },
        columns: [
            { data: 'debtor_name' },
            { data: 'total_pieces' },
            { data: 'total_invoice_amount' }
        ]
    });

    $('#filterBtn').click(function() {
        table.ajax.reload();
    });

    $('#exportCsvBtn').click(function() {
        let range = $('#dateRange').val();
        this.href = "{{ route('admin.reports.debtor-wise.export') }}?date_range=" + range;
    });
});
</script>
@endpush