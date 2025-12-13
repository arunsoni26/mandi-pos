<div class="modal-header bg-success text-white">
    <h5 class="modal-title"><i class="fa fa-upload me-2"></i> Upload Customer Documents — {{ $customer->name }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="docsForm" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <input type="hidden" name="customer_id" value="{{ $customer->id }}">

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">GST Year</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    <select class="form-select" name="gst_year_id" id="docs_gst_year" required>
                        <option value="">Select GST Year</option>
                        @foreach($years as $y)
                            <option value="{{ $y->id }}">{{ $y->label }}</option>
                        @endforeach
                    </select>
                </div>
                <small class="text-primary d-inline-block mt-1" id="quickAddYear" style="cursor:pointer;">
                    <i class="fa fa-plus"></i> Add new GST year
                </small>
            </div>
        </div>

        @php
            $sections = $types; // key => label
        @endphp

        @foreach($sections as $typeKey => $typeLabel)
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="fa fa-folder me-2"></i>{{ $typeLabel }}</strong>
                    <button type="button" class="btn btn-sm btn-outline-primary addRow" data-type="{{ $typeKey }}">
                        <i class="fa fa-plus"></i> Add {{ $typeLabel }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="docRows" id="rows_{{ $typeKey }}">
                        {{-- first row --}}
                        <div class="row g-2 align-items-end docRow">
                            <div class="col-md-8">
                                <label class="form-label mb-1">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                                    <input type="text" class="form-control" name="docs[{{ $typeKey }}][0][description]" placeholder="Short description (optional)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">File</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-file"></i></span>
                                    <input type="file" class="form-control" name="docs[{{ $typeKey }}][0][file]">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <small class="text-muted">Max file size 10MB each.</small>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        <button type="submit" id="docsSubmit" class="btn btn-success"><i class="fa fa-save"></i> Upload</button>
    </div>
</form>

<script>
(function(){
    // Quick add GST year
    $('#quickAddYear').on('click', function(){
        const lbl = prompt('Enter GST Year (e.g., 2024-2025)');
        if(!lbl) return;
        $.post("{{ route('admin.gst_years.save') }}", {
            _token: "{{ csrf_token() }}",
            label: lbl
        }, function(res){
            if(res.success){
                $('#docs_gst_year').prepend(`<option value="${res.data.id}">${res.data.label}</option>`)
                                   .val(res.data.id);
            }
        });
    });

    // Add row per section
    $('.addRow').on('click', function(){
        const type = $(this).data('type');
        const $wrap = $('#rows_' + type);
        const index = $wrap.find('.docRow').length;

        const row = `
            <div class="row g-2 align-items-end docRow mt-2">
                <div class="col-md-8">
                    <label class="form-label mb-1">Description</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                        <input type="text" class="form-control" name="docs[${type}][${index}][description]" placeholder="Short description (optional)">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">File</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-file"></i></span>
                        <input type="file" class="form-control" name="docs[${type}][${index}][file]">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger removeRow"><i class="fa fa-times"></i></button>
                </div>
            </div>`;
        $wrap.append(row);
    });

    // Remove row
    $(document).on('click', '.removeRow', function(){
        $(this).closest('.docRow').remove();
    });

    // Submit
    $('#docsForm').on('submit', function(e){
        e.preventDefault();
        const btn = $('#docsSubmit');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Uploading...');

        const fd = new FormData(this);

        $.ajax({
            url: "{{ route('admin.customers.docs.save', $customer->id) }}",
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res){
                if(res.success){
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                    $('#ajaxModal').modal('hide');
                    // refresh table on index page
                    if ($('#docsTable').length) {
                        $('#docsTable tbody').empty();
                        // trigger reload in parent view
                        $('[id="filterYear"], [id="filterType"]').first().trigger('change');
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(res.message || 'Upload failed');
                }
            },
            error: function(xhr){
                let msg = 'Upload failed';
                if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                if (typeof toastr !== 'undefined') toastr.error(msg);
            },
            complete: function(){
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Upload');
            }
        });
    });
})();
</script>
