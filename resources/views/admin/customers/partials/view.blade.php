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
                    <!-- <i class="fa fa-user-circle fa-4x"></i> -->
                    <!-- <img style="width:100px; height:auto;" src="{{ asset('storage/customers/l8Ya9OU6LSA6WlyOfjnCFpbJVuo47rN5iBjmQl6q.png') }}" alt="Customer Image"> -->
                    <img style="width:100px; height:auto;" src="{{ asset($customer->profile_pic) }}" alt="Customer Image">
                </div>
                <div>
                    <h4 class="mb-0">{{ $customer->name }}</h4>
                    <small><i class="fa fa-phone"></i> {{ $customer->mobile ?? 'N/A' }}</small>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body">
                {{-- Section: Basic Info --}}
                <h5 class="mb-3 border-bottom pb-2"><i class="fa fa-info-circle"></i> Basic Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>PAN:</strong> {{ $customer->pan ?? 'N/A' }}</div>
                </div>

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