<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="../dashboard/index.html" class="b-brand text-primary text-center">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('assets') }}/images/mandi-pos-logo-1.png" alt="" class="w-50" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Dashboard</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-dashboard"></i>
                        </span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                
                
                @if(canDo('permissions','can_add'))
                    <li class="pc-item {{ request()->routeIs('admin.role-permissions') ? 'active' : '' }}">
                        <a class='pc-link' href='{{ route('admin.role-permissions') }}'>
                            <span class="pc-micon">
                                <i class="fas fa-user-lock"></i>
                            </span>
                            <span class="align-middle">Roles & Permissions</span>
                        </a>
                    </li>
                @endif

                @if(canDo('users','can_add'))
                    <li class="pc-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                        <a class='pc-link' href='{{ route('admin.users.index') }}'>
                            <span class="pc-micon">
                                <i class="fas fa-users"></i>
                            </span>
                            <span class="align-middle">Users</span>
                        </a>
                    </li>
                @endif
                
                @if(canDo('customers','can_add'))
                    <li class="pc-item {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                        <a class='pc-link' href='{{ route('admin.customers.index') }}'>
                            <span class="pc-micon">
                                <i class="fas fa-user-tie"></i>
                            </span>
                            <span class="align-middle">Customers</span>
                        </a>
                    </li>
                @endif
                
                @if(canDo('customers','can_add'))
                    <li class="pc-item {{ request()->routeIs('admin.pos.creditors.invoices') ? 'active' : '' }}">
                        <a class='pc-link' href='{{ route('admin.pos.creditors.invoices') }}'>
                            <span class="pc-micon">
                                <i class="fas fa-user-tie"></i>
                            </span>
                            <span class="align-middle">Creditor Invoices</span>
                        </a>
                    </li>
                @endif
                
                @if(canDo('customers','can_add'))
                    <li class="pc-item {{ request()->routeIs('admin.pos.debitors.invoices') ? 'active' : '' }}">
                        <a class='pc-link' href='{{ route('admin.pos.debitors.invoices') }}'>
                            <span class="pc-micon">
                                <i class="fas fa-user-tie"></i>
                            </span>
                            <span class="align-middle">Debitor Invoices</span>
                        </a>
                    </li>
                @endif

            </ul>
            <!-- <div class="pc-navbar-card bg-primary rounded">
                <h4 class="text-white">Explore full code</h4>
                <p class="text-white opacity-75">Buy now to get full access of code files</p>
                <a href="https://codedthemes.com/item/berry-bootstrap-5-admin-template/" target="_blank"
                    class="btn btn-light text-primary">
                    Buy Now
                </a>
            </div> -->
            <div class="w-100 text-center">
                <div class="badge theme-version badge rounded-pill bg-light text-dark f-12"></div>
            </div>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->