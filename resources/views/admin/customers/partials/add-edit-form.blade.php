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
    @if(!empty($customerType))
        <input type="hidden" name="customer_type" value="{{ $customerType }}">
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

            {{-- Mobile --}}
            <div class="col-md-6">
                <label class="form-label">Mobile No</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="tel" name="mobile" class="form-control" value="{{ $customer->mobile ?? '' }}">
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

            {{-- Profile Pic --}}
            @if (isset($customer->profile_pic) && !empty($customer->profile_pic))
                <div class="col-md-3">
                    <label class="form-label">Profile Pic</label>
                    <img src="" alt="user-image" class="user-avtar">
                </div>
            @else
                <div class="col-md-3">
                    <label class="form-label">Profile Pic</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="file" name="profile_pic" class="form-control" value="{{ $customer->profile_pic ?? '' }}" required>
                    </div>
                </div>
            @endif

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
                            toastr.success(data.message);
                            @if (!isset($isPOS) && !$isPOS)
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            @else
                                // Hide Select2 section
                                $('#creditorSelectionSection').hide();

                                // Show selected creditor
                                showSelectedCreditor(data.customer);

                                onCreditorSelected(data.customer.id);
                            @endif
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
