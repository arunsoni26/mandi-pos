@extends('layouts.admin-app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0"><i class="fa fa-folder-open me-2"></i> Customer Docs — {{ $customer->name }}</h5>
                <small class="opacity-75">Manage uploads by GST year & type</small>
            </div>
            <div class="d-flex gap-2">
                <select id="filterYear" class="form-select form-select-sm">
                    <option value="">All GST Years</option>
                    @foreach($years as $y)
                        <option value="{{ $y->id }}">{{ $y->label }}</option>
                    @endforeach
                </select>
                <select id="filterType" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach(\App\Models\CustomerDocument::TYPES as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
                <button class="btn btn-light btn-sm" id="btnAddGstYear"><i class="fa fa-plus"></i> GST Year</button>
                <button class="btn btn-success btn-sm" id="btnUploadDocs"><i class="fa fa-upload"></i> Upload Docs</button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="docsTable" class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>GST Year</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>File</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
                            <th style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody><!-- ajax --></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Reusable Modal --}}
<div class="modal fade" id="ajaxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content" id="ajaxModalContent"></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const customerId = {{ $customer->id }};
    const tableBody = $('#docsTable tbody');

    function loadTable(){
        $.get("{{ route('admin.customers.docs.list', $customer->id) }}", {
            gst_year_id: $('#filterYear').val(),
            doc_type: $('#filterType').val()
        }, function(res){
            let rows = '';
            (res.data || []).forEach(r => {
                rows += `<tr>
                    <td>${r.year ?? '-'}</td>
                    <td>${r.type ?? '-'}</td>
                    <td>${r.desc ?? '-'}</td>
                    <td>${r.file ?? ''}</td>
                    <td>${r.by ?? '-'}</td>
                    <td>${r.date ?? '-'}</td>
                    <td>${r.actions ?? ''}</td>
                </tr>`;
            });
            tableBody.html(rows);
        });
    }

    // initial
    loadTable();

    $('#filterYear, #filterType').on('change', loadTable);

    $('#btnUploadDocs').on('click', function(){
        $.post("{{ route('admin.customers.docs.form', $customer->id) }}",
            {_token: "{{ csrf_token() }}"},
            function(html){
                $('#ajaxModalContent').html(html);
                $('#ajaxModal').modal('show');
            }
        );
    });

    $('#btnAddGstYear').on('click', function(){
        const lbl = prompt('Enter GST Year (e.g., 2024-2025)');
        if(!lbl) return;
        $.post("{{ route('admin.gst_years.save') }}", {
            _token: "{{ csrf_token() }}",
            label: lbl
        }, function(res){
            if(res.success){
                // refresh filterYear and form dropdowns (if open)
                $('#filterYear').prepend(`<option value="${res.data.id}">${res.data.label}</option>`);
                $('#filterYear').val(res.data.id).trigger('change');
                // also notify form (if present)
                if($('#docs_gst_year').length){
                    $('#docs_gst_year').prepend(`<option value="${res.data.id}">${res.data.label}</option>`)
                                        .val(res.data.id);
                }
            }
        });
    });

    // Delete single doc
    $(document).on('click', '.deleteDoc', function(){
        if(!confirm('Delete this document?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: "{{ route('admin.customers.docs.delete', [$customer->id, 0]) }}".replace('/0', '/' + id),
            type: 'DELETE',
            data: {_token: "{{ csrf_token() }}"},
            success: function(res){
                if(res.success) loadTable();
            }
        });
    });
})();
</script>
@endpush
