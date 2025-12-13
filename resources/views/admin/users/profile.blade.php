@extends('layouts.admin-app')

<style>
    .description-text {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

@section('content')
<div class="container-fluid p-0">

    <h1 class="h3 mb-3">Settings</h1>

    <div class="row">
        <div class="col-md-3 col-xl-2">

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Settings</h5>
                </div>

                <div class="list-group list-group-flush" role="tablist">
                    <a class="list-group-item list-group-item-action {{ (session('active_tab', old('active_tab', 'account')) === 'account') ? 'active' : '' }}"
                        data-bs-toggle="list" href="#account" role="tab"> Basic Details
                    </a>
                    <!-- <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#password" role="tab">
                        Password
                    </a> -->
                    <a class="list-group-item list-group-item-action {{ session('active_tab') === 'password' ? 'active' : '' }}"
                        data-bs-toggle="list" href="#password" role="tab">Password
                    </a>
                    @if($userInfo->role_id == 1)
                    <a class="list-group-item list-group-item-action  {{ session('active_tab') === 'news' ? 'active' : '' }}"
                        data-bs-toggle="list" href="#news" role="tab">
                        News
                    </a>
                    <a class="list-group-item list-group-item-action {{ session('active_tab') === 'gallery' ? 'active' : '' }}"
                        data-bs-toggle="list" href="#gallery" role="tab">
                        Gallery
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-9 col-xl-10">
            <div class="tab-content">
                <!-- Basic info tab -->
                <div class="tab-pane fade {{ session('active_tab', 'account') === 'account' ? 'show active' : '' }}" id="account" role="tabpanel">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">User info</h5>
                        </div>

                        <div class="card-body">
                            @if(session('success'))
                            <div class="alert alert-success" style="padding: 1rem;">
                                {{ session('success') }}
                                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                            @if($errors->any())
                            <div class="alert alert-danger" style="padding: 1rem;">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                            <form action="{{ route('admin.settings.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label" for="inputUsername">Name</label>
                                            <input type="text" name="name" value="{{ $userInfo->name ?? '' }}" class="form-control" id="inputUsername" placeholder="Username">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="inputUseremail">Email</label>
                                            <input type="text" name="email" value="{{ $userInfo->email ?? '' }}" class="form-control" id="inputUseremail" placeholder="Email" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <img alt="Charles Hall" src="img/avatars/avatar.jpg" class="rounded-circle img-responsive mt-2"
                                                width="128" height="128" />
                                            <div class="mt-2">
                                                <span class="btn btn-primary"><i class="fas fa-upload"></i> Upload</span>
                                            </div>
                                            <small>For best results, use an image at least 128px by 128px in .jpg format</small>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Password tab -->
                <div class="tab-pane fade {{ session('active_tab') === 'password' ? 'show active' : '' }}" id="password" role="tabpanel">
                    <div class="card">
                        <div class="card-body" style="padding: 1rem;">
                            @if(session('success') && session('active_tab') === 'password')
                            <div class="alert alert-success" style="padding: 1rem;">
                                {{ session('success') }}
                                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif

                            @if($errors->any() && session('active_tab') === 'password')
                            <div class="alert alert-danger" style="padding: 1rem;">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                            <h5 class="card-title">Password</h5>

                            <form action="{{ route('admin.settings.password') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="inputPasswordCurrent">Current password</label>
                                    <input required type="password" name="current_password" class="form-control" id="inputPasswordCurrent">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="inputPasswordNew">New password</label>
                                    <input required type="password" name="new_password" class="form-control" id="inputPasswordNew">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="inputPasswordNew2">Verify password</label>
                                    <input required type="password" name="new_password_confirmation" class="form-control" id="inputPasswordNew2">
                                </div>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>


                        </div>
                    </div>
                </div>

                <!-- News tab -->
                <div class="tab-pane fade {{ session('active_tab') === 'news' ? 'show active' : '' }}" id="news" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">News</h5>
                        </div>

                        <div class="card-body">
                            @if(session('success') && session('active_tab') === 'news')
                            <div class="alert alert-success" style="padding: 1rem;">
                                {{ session('success') }}
                                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif

                            @if($errors->any() && session('active_tab') === 'news')
                            <div class="alert alert-danger" style="padding: 1rem;">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                            <form action="{{ route('admin.news.add') }}" method="POST">
                                @csrf
                                <div id="newsFields">
                                    <div class="news-item row mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Description</label>
                                            <textarea rows="3" required name="description[]" class="form-control" placeholder="Write something for today updates...."></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Color</label>
                                            <div class="d-flex align-items-center">
                                                <input type="color" name="color[]" value="#000000" class="form-control form-control-color" style="max-width: 60px;">
                                                <span class="ms-2 colorName">#000000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="d-flex justify-content-end ">
                                    <button type="submit" class="btn btn-primary m-3 me-1">Save All</button>
                                    <button type="button" id="addMoreBtn" class="btn btn-secondary m-3">+ Add More</button>
                                </div>
                            </form>

                            @if($news->count())
                            <hr>
                            <h5 class="mb-3">Your News</h5>
                            <div class="running-border" style="max-height: 500px; overflow-y: auto; padding:1rem;">
                                @foreach($news as $item)
                                @php
                                $words = explode(' ', $item->description);
                                $shortDescription = implode(' ', array_slice($words, 0, 5));
                                $isLong = count($words) > 5;
                                @endphp

                                <div class="p-2 mb-2 border rounded d-flex justify-content-between align-items-center news-item zoom-item {{ $item->trashed() ? 'bg-light' : '' }}">
                                    <div style="flex:1; max-width: 65%;">
                                        <p class="mb-1 description-text">
                                            {{ $shortDescription }}
                                            @if($isLong)
                                            ... <a href="javascript:void(0)"
                                                class="text-primary readMoreBtn"
                                                data-description="{{ $item->description }}">
                                                Read more
                                            </a>
                                            @endif
                                        </p>
                                        <small class="text-muted">
                                            {{ $item->created_at->format('d M Y, h:i A') }}
                                            @if($item->trashed()) — <span class="text-danger">Deleted</span> @endif
                                        </small>
                                    </div>
                                    <div style="width: 100px;">
                                        <p class="mb-1">{{ $item->color }}</p>
                                    </div>
                                    <div class="text-end">
                                        @if($item->trashed())
                                        <form action="{{ route('admin.news.restore', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Restore this news?')">
                                                <i class="bi bi-arrow-clockwise"></i> Restore
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-secondary" disabled>Edit</button>
                                        @else
                                        <button type="button"
                                            class="btn btn-sm btn-primary me-1 editNewsBtn"
                                            data-id="{{ $item->id }}"
                                            data-description="{{ $item->description }}"
                                            data-color="{{ $item->color }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this news?')">
                                                Delete
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-muted">No news added yet.</p>
                            @endif
                        </div>

                        <!-- Edit News Modal -->
                        <div class="modal fade" id="editNewsModal" tabindex="-1" aria-labelledby="editNewsLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="editNewsForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editNewsLabel">Edit News</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" id="editDescription" rows="3" class="form-control"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Color</label>
                                                <input type="color" name="color" id="editColor" class="form-control form-control-color" style="max-width: 60px;">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Read More Modal -->
                        <div class="modal fade" id="readMoreModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Full Description</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="readMoreContent">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery tab -->
            <div class="tab-pane fade {{ session('active_tab') === 'gallery' ? 'show active' : '' }}" id="gallery" role="tabpanel">
                <div class="card">
                    <div class="card-body" style="padding: 1rem;">
                        @if(session('success') && session('active_tab') === 'gallery')
                        <div class="alert alert-success" style="padding: 1rem;">
                            {{ session('success') }}
                            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if($errors->any() && session('active_tab') === 'gallery')
                        <div class="alert alert-danger" style="padding: 1rem;">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        <h5 class="card-title">Gallery</h5>
                        <form action="{{ route('admin.banner.add') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="inputImage">Image</label>
                                <input required type="file" accept="image/*" name="image" class="form-control" id="inputImage">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="bannerDesc">Description</label>
                                <textarea rows="3" id="bannerDesc" required name="bannerdescription" class="form-control" placeholder="Write something here...."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>

                    <div>
                        <ul class="nav nav-tabs mt-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ session('active_tab') === 'trash' ? '' : 'active' }}" data-bs-toggle="tab" href="#galleryTab" role="tab">Gallery</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ session('active_tab') === 'trash' ? 'active' : '' }}" data-bs-toggle="tab" href="#trashTab" role="tab">Trash</a>
                            </li>
                        </ul>

                        <div class="tab-content border border-top-0 p-3">
                            <!-- Gallery Tab -->
                            <div class="tab-pane fade {{ session('active_tab') === 'trash' ? '' : 'show active' }}" id="galleryTab" role="tabpanel">
                                <div class="row">
                                    @forelse($gallery as $item)
                                    <div class="col-md-3 mb-3 zoom-item">
                                        <div class="card">
                                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" style="height:150px; object-fit:cover;">
                                            <div class="card-body">
                                                <!-- <p class="card-text">{{ Str::limit($item->description, 50) }}</p> -->
                                                <form action="{{ route('admin.banner.delete', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger w-100">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted">No images found.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Trash Tab -->
                            <div class="tab-pane fade {{ session('active_tab') === 'trash' ? 'show active' : '' }}" id="trashTab" role="tabpanel">
                                <div class="row">
                                    @forelse($trash as $item)
                                    <div class="col-md-3 mb-3 zoom-item">
                                        <div class="card border-warning">
                                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" style="height:150px; object-fit:cover;">
                                            <div class="card-body">
                                                <!-- <p class="card-text">{{ Str::limit($item->description, 50) }}</p> -->
                                                <form action="{{ route('admin.banner.restore', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="btn btn-sm btn-success w-100">Restore</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-muted">No deleted images.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.getElementById('inputColor');
        const colorName = document.getElementById('colorName');

        colorInput.addEventListener('input', function() {
            colorName.textContent = colorInput.value;
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('newsFields');
        const addBtn = document.getElementById('addMoreBtn');

        addBtn.addEventListener('click', function() {
            const newField = document.querySelector('.news-item').cloneNode(true);
            newField.querySelectorAll('textarea').forEach(el => el.value = '');
            newField.querySelectorAll('input[type=color]').forEach(el => el.value = '#000000');
            newField.querySelectorAll('.colorName').forEach(el => el.textContent = '#000000');
            container.appendChild(newField);
        });

        container.addEventListener('input', function(e) {
            if (e.target.type === 'color') {
                e.target.closest('.d-flex').querySelector('.colorName').textContent = e.target.value;
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // read model
        document.querySelectorAll('.readMoreBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('readMoreContent').innerText = this.getAttribute('data-description');
                new bootstrap.Modal(document.getElementById('readMoreModal')).show();
            });
        });

        // edit model
        const editButtons = document.querySelectorAll('.editNewsBtn');
        const editForm = document.getElementById('editNewsForm');
        const editDescription = document.getElementById('editDescription');
        const editColor = document.getElementById('editColor');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const description = this.dataset.description;
                const color = this.dataset.color;

                editForm.action = `/news/${id}`; // Route for update
                editDescription.value = description;
                editColor.value = color;

                const modal = new bootstrap.Modal(document.getElementById('editNewsModal'));
                modal.show();
            });
        });
    });
</script>
@endpush