<main class="col-md-12 ms-sm-auto col-lg-12 px-md-4 mt-4 mb-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Permissions for <b style="color:crimson;">{{ ucfirst($role->name) }}</b></h1>
    </div>

    <div class="alert alert-light">
        <h6 class="mb-0"><i class="bi bi-info-circle-fill"></i> Toggle the switches to grant or revoke access for this role.</h6>
    </div>

    <form id="saveRole" method="POST">
        @csrf
        <input type="hidden" name="role_id" value="{{ $role->id }}">

        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>
                        Can View Nav <br>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input check-all-column" data-column="can_view_nav">
                        </div>
                    </th>
                    <th>
                        Can Access <br>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input check-all-column" data-column="can_access">
                        </div>
                    </th>
                    <th>
                        Can Add <br>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input check-all-column" data-column="can_add">
                        </div>
                    </th>
                    <th>
                        Can View <br>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input check-all-column" data-column="can_view">
                        </div>
                    </th>
                    <th>
                        Can Edit <br>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input check-all-column" data-column="can_edit">
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($modules as $module)
                    @php
                        // match by module_id not id, since rolePermissions likely stores module_id
                        $existing = $rolePermissions->firstWhere('module_id', $module->id);
                    @endphp
                    <tr>
                        <td>{{ ucfirst($module->name) }}</td>

                        <td class="text-center">
                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input permission-checkbox"
                                    data-column="can_view_nav"
                                    name="permissions[{{ $module->id }}][can_view_nav]"
                                    value="1"
                                    {{ $existing && $existing->can_view_nav ? 'checked' : '' }}>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="form-check form-switch">
                                <input 
                                    type="checkbox"
                                    class="form-check-input permission-checkbox"
                                    data-column="can_access"
                                    name="permissions[{{ $module->id }}][can_access]"
                                    value="1"
                                    {{ $existing && $existing->can_access ? 'checked' : '' }}>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input permission-checkbox"
                                    data-column="can_add"
                                    name="permissions[{{ $module->id }}][can_add]"
                                    value="1"
                                    {{ $existing && $existing->can_add ? 'checked' : '' }}>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input permission-checkbox"
                                    data-column="can_view"
                                    name="permissions[{{ $module->id }}][can_view]"
                                    value="1"
                                    {{ $existing && $existing->can_view ? 'checked' : '' }}>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input permission-checkbox"
                                    data-column="can_edit"
                                    name="permissions[{{ $module->id }}][can_edit]"
                                    value="1"
                                    {{ $existing && $existing->can_edit ? 'checked' : '' }}>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-grid gap-2 d-flex justify-content-center">
            <button id="submit-button" class="btn btn-success fw-bold" type="submit">
                <i class="bi bi-check-circle"></i> Save Changes
            </button>
        </div>
    </form>
</main>

<script>
    var formElement = document.getElementById('saveRole');
    var submitButton = document.getElementById('submit-button');

    if (formElement) {
        formElement.addEventListener('submit', function(event) {
            event.preventDefault();
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                url: "{{ route('admin.update-role-permission') }}",
                data: $(formElement).serialize(),
                success: function(data) {
                    if (data.code === 200) {
                        toastr.success(data.msg);
                        $('#editModal').modal('hide');
                    } else {
                        toastr.error(data.msg);
                    }
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="bi bi-check-circle"></i> Save Changes';
                },
                error: function() {
                    toastr.error("An error occurred while saving.");
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="bi bi-check-circle"></i> Save Changes';
                }
            });
        });
    }

    // Column toggle (all same type across modules)
    $(document).on('change', '.check-all-column', function () {
        let column = $(this).data('column');
        let isChecked = $(this).is(':checked');
        $('.permission-checkbox[data-column="' + column + '"]').prop('checked', isChecked).trigger('change');
    });

    // Row toggle (all permissions for one module)
    $(document).on('change', '.check-all-row', function () {
        let row = $(this).closest('tr');
        let isChecked = $(this).is(':checked');
        row.find('.permission-checkbox').prop('checked', isChecked).trigger('change');
    });

</script>
