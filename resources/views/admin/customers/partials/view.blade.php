<div class="modal-header text-primary">
    <h5 class="modal-title"><i class="fa fa-user"></i> View Customer</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" id="viewCustomerContent">
    <div class="container py-3">
        {{-- Profile Card --}}
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
            {{-- Header --}}
            <div class="text-primary p-4 d-flex align-items-center">
                <div class="me-3">
                    <i class="fa fa-user-circle fa-4x"></i>
                </div>
                <div>
                    <h4 class="mb-0">{{ $customer->name }}</h4>
                    <small><i class="fa fa-envelope"></i> {{ $customer->email ?? 'N/A' }}</small><br>
                    <small><i class="fa fa-phone"></i> {{ $customer->mobile ?? 'N/A' }}</small>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body">
                {{-- Section: Basic Info --}}
                <h5 class="mb-3 border-bottom pb-2"><i class="fa fa-info-circle"></i> Basic Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Father's Name:</strong> {{ $customer->father_name ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Group:</strong> {{ $customer->group->name ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Code:</strong> {{ $customer->code ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Client Type Status:</strong> {{ ucfirst($customer->client_type_status ?? 'N/A') }}</div>
                    <div class="col-md-6 mb-3"><strong>Date of Birth:</strong> {{ $customer->dob ? $customer->dob->format('d-m-Y') : 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>City:</strong> {{ $customer->city ?? 'N/A' }}</div>
                </div>

                {{-- Section: Documents --}}
                <h5 class="mt-4 mb-3 border-bottom pb-2"><i class="fa fa-file-alt"></i> Documents</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>GST Name:</strong> {{ $customer->gst_name ?? 'N/A' }}<br>
                        <strong>GST No:</strong> {{ $customer->gst ?? 'N/A' }}<br>
                        @if($customer->gst_doc)
                            <a href="{{ asset('uploads/'.$customer->gst_doc) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fa fa-eye"></i> View GST Doc
                            </a>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>PAN:</strong> {{ $customer->pan ?? 'N/A' }}<br>
                        @if($customer->pan_doc)
                            <a href="{{ asset('uploads/'.$customer->pan_doc) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fa fa-eye"></i> View PAN Doc
                            </a>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Aadhaar:</strong> {{ $customer->aadhar ?? 'N/A' }}<br>
                        @if($customer->aadhar_doc)
                            <a href="{{ asset('uploads/'.$customer->aadhar_doc) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="fa fa-eye"></i> View Aadhaar Doc
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Section: Address --}}
                <h5 class="mt-4 mb-3 border-bottom pb-2"><i class="fa fa-map-marker-alt"></i> Address</h5>
                <p>{{ $customer->address ?? 'N/A' }}</p>

                {{-- Section: Status --}}
                <h5 class="mt-4 mb-3 border-bottom pb-2">
                    Status: {!! $customer->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}
                </h5>
                <p>
                    Dashboard: {!! $customer->hide_dashboard ? '<span class="badge bg-success">Visible</span>' : '<span class="badge bg-secondary">Hidden</span>' !!}
                </p>
            </div>
        </div>
    </div>


</div>
