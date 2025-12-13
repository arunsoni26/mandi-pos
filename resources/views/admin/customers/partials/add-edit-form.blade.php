<div class="modal-header">
    <h5 class="modal-title text-primary">
        <i class="fas fa-user-edit me-2"></i> {{ isset($customer) ? 'Edit' : 'Add' }} Customer
    </h5>
    <button type="button" class="btn-close btn-close-primary" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="customerForm" novalidate="" enctype="multipart/form-data" >
    @csrf
    @if(!empty($customer))
        <input type="hidden" name="id" value="{{ $customer->id }}">
    @endif

    <div class="modal-body">
        <div class="row g-3">

            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="name" class="form-control" value="{{ $customer->name ?? '' }}" required>
                </div>
            </div>

            {{-- GST Name --}}
            <div class="col-md-6">
                <label class="form-label">GST Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <input type="text" name="gst_name" class="form-control" value="{{ $customer->gst_name ?? '' }}" required>
                </div>
            </div>

            {{-- Father's Name --}}
            <div class="col-md-6">
                <label class="form-label">Father's Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                    <input type="text" name="fathers_name" class="form-control" value="{{ $customer->father_name ?? '' }}" required>
                </div>
            </div>

            {{-- PAN --}}
            <div class="col-md-3">
                <label class="form-label">PAN</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <input type="text" name="pan" class="form-control" value="{{ $customer->pan ?? '' }}" required>
                </div>
            </div>

            {{-- PAN Document --}}
            <div class="col-md-3">
                <label class="form-label">PAN Doc</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-file-arrow-up"></i></span>
                    <input type="file" name="pan_doc" class="form-control">
                </div>
                @if(!empty($customer?->pan_doc))
                    <a class="small d-block mt-1" target="_blank" href="{{ asset('storage/'.$customer->pan_doc) }}">View current</a>
                @endif
            </div>

            {{-- Client Type --}}
            <div class="col-md-6">
                <label class="form-label">Client Type</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                    <select name="client_type_status" class="form-select" required>
                        <option value="">Choose...</option>
                        <option value="gst"   @selected(($customer->client_type_status ?? '') === 'gst')>GST</option>
                        <option value="itr"   @selected(($customer->client_type_status ?? '') === 'itr')>ITR</option>
                        <option value="telly" @selected(($customer->client_type_status ?? '') === 'telly')>Telly</option>
                    </select>
                </div>
            </div>

            {{-- Code --}}
            <div class="col-md-6">
                <label class="form-label">Code</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                    <input type="text" name="code" class="form-control" value="{{ $customer->code ?? '' }}" required>
                </div>
            </div>

            {{-- Mobile --}}
            <div class="col-md-6">
                <label class="form-label">Mobile No</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="tel" name="mobile" class="form-control" value="{{ $customer->mobile ?? '' }}">
                </div>
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" value="{{ $customer->email ?? '' }}" required>
                </div>
            </div>

            @if(isset($customer) && !empty($customer->email))
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                    </div>
                </div>
            @endif

            {{-- City --}}
            <div class="col-md-6">
                <label class="form-label">City</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                    <input type="text" name="city" class="form-control" value="{{ $customer->city ?? '' }}">
                </div>
            </div>

            {{-- Group --}}
            <div class="col-md-6">
                <label class="form-label">Group</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                    <select name="group_id" class="form-select" id="groupSelect" required>
                        <option value="">Select Group</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" @selected(($customer->group_id ?? null) == $g->id)>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Optional: small quick-add link (hook up later if you like) --}}
                {{-- <small class="text-primary d-inline-block mt-1" id="addGroupLink"><i class="fas fa-plus"></i> Add new group</small> --}}
            </div>

            {{-- Date of Birth --}}
            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" name="dob" class="form-control" value="{{ $customer->dob ?? '' }}">
                </div>
            </div>

            {{-- GST --}}
            <div class="col-md-3">
                <label class="form-label">GST</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                    <input type="text" name="gst" class="form-control" value="{{ $customer->gst ?? '' }}">
                </div>
            </div>

            {{-- GST Doc --}}
            <div class="col-md-3">
                <label class="form-label">GST Doc</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-file-arrow-up"></i></span>
                    <input type="file" name="gst_doc" class="form-control">
                </div>
                @if(!empty($customer?->gst_doc))
                    <a class="small d-block mt-1" target="_blank" href="{{ asset('storage/'.$customer->gst_doc) }}">View current</a>
                @endif
            </div>

            {{-- Aadhar --}}
            <div class="col-md-3">
                <label class="form-label">Aadhar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                    <input type="text" name="aadhar" class="form-control" value="{{ $customer->aadhar ?? '' }}">
                </div>
            </div>

            {{-- Aadhar Doc --}}
            <div class="col-md-3">
                <label class="form-label">Aadhar Doc</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-file-arrow-up"></i></span>
                    <input type="file" name="aadhar_doc" class="form-control">
                </div>
                @if(!empty($customer?->aadhar_doc))
                    <a class="small d-block mt-1" target="_blank" href="{{ asset('storage/'.$customer->aadhar_doc) }}">View current</a>
                @endif
            </div>

            {{-- Address --}}
            <div class="col-12">
                <label class="form-label">Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-location-dot"></i></span>
                    <textarea name="address" rows="2" class="form-control" placeholder="Full Address">{{ $customer->address ?? '' }}</textarea>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Close
        </button>
        <button type="submit" id="customerFormSubmit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Save
        </button>
    </div>
</form>

{{-- Small style tweak to keep icons tidy --}}
<style>
    #customerModal .input-group-text { min-width: 42px; justify-content: center; }
</style>

<script>
(function () {
    const customerForm = document.getElementById('customerForm');
    const submitButton = document.getElementById('customerFormSubmit');

    if (customerForm) {
        customerForm.addEventListener('submit', function (e) {
            // console.log(event.target.checkValidity());
            if (!event.target.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                scrollToFirstInvalidField();
            } else {
                event.preventDefault();
                event.stopPropagation();
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> loading';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: "{{route('admin.customers.save')}}",
                    cache : false,
                    processData: false,
                    contentType: false,
                    data: new FormData(customerForm),
                    success: function (data) {
                        console.log('user_data----->>>', data);
                        if (data.code == 200) {
                            toastr.success(data.msg);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error(data.msg);
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Save';
                        }
                    },
                    error: function (err) {
                        console.log('err----->>>', err);
                        toastr.error("User role not found");
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Save';
                    }
                });
            }
            event.target.classList.add('was-validated');
        }, false);
    }

    function scrollToFirstInvalidField() {
        const firstInvalidField = $('form .form-control:invalid')[0];
        if (firstInvalidField) {
            firstInvalidField.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            setTimeout(() => {
                firstInvalidField.focus();
            }, 1000);
        }
    }

})();
</script>
