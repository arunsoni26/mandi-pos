<div class="modal-header">
    <h5 class="modal-title text-primary">
        <i class="fas fa-user-edit me-2"></i> {{ isset($user) ? 'Edit' : 'Add' }} User
    </h5>
    <button type="button" class="btn-close btn-close-primary" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="userForm" novalidate="" enctype="multipart/form-data" >
    @csrf
    @if(!empty($user))
        <input type="hidden" name="id" value="{{ $user->id }}">
    @endif

    <div class="modal-body">
        <div class="row g-3">

            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="name" class="form-control" value="{{ $user->name ?? '' }}" required>
                </div>
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" value="{{ $user->email ?? '' }}" required>
                </div>
            </div>
            

            @if(!isset($user) && empty($user->email))
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

        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Close
        </button>
        <button type="submit" id="userFormSubmit" class="btn btn-success">
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
    const userForm = document.getElementById('userForm');
    const submitButton = document.getElementById('userFormSubmit');

    if (userForm) {
        userForm.addEventListener('submit', function (e) {
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
                    url: "{{route('admin.users.save')}}",
                    cache : false,
                    processData: false,
                    contentType: false,
                    data: new FormData(userForm),
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
