<div class="modal-header">
    <h5 class="modal-title">{{ isset($group) ? 'Edit Group' : 'Add Group' }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="groupForm">
    <div class="modal-body">
        <input type="hidden" name="id" value="{{ $group->id ?? '' }}">
        <div class="mb-3">
            <label class="form-label">Group Name</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa fa-users"></i>
                </span>
                <input type="text" name="name" class="form-control" value="{{ $group->name ?? '' }}" placeholder="Enter group name" required>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" id="groupFormSubmit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>

<script>
(function () {
    const customerForm = document.getElementById('groupForm');
    const submitButton = document.getElementById('groupFormSubmit');

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
                    url: "{{route('admin.customers.groups.save')}}",
                    cache : false,
                    processData: false,
                    contentType: false,
                    data: new FormData(customerForm),
                    success: function (data) {
                        console.log('user_data----->>>', data);
                        if (data.code == 200) {
                            toastr.success(data.msg);
                            setTimeout(() => {
                                loadGroups();
                                $('#editModal').modal('hide');
                            }, 1000);
                        } else {
                            toastr.error(data.msg);
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Save';
                        }
                    },
                    error: function (err) {
                        console.log('err----->>>', err);
                        toastr.error(err.statusText);
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