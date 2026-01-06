<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-text mx-3">Perpustakaan</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ is_current_role_route('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dashboard_route() }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @if (auth()->user()->role === \App\Models\User::ROLES['Admin'])
        <!-- Pustakawan (Admin Only) -->
        <li class="nav-item {{ is_current_role_route('librarians.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ dynamic_route('librarians.index') }}">
                <i class="fas fa-fw fa-user-tie"></i>
                <span>Pustakawan</span>
            </a>
        </li>
    @endif

    <!-- Members -->
    <li class="nav-item {{ is_current_role_route('members.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dynamic_route('members.index') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>Member</span>
        </a>
    </li>

    <!-- Books -->
    <li class="nav-item {{ is_current_role_route('books.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dynamic_route('books.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Buku</span>
        </a>
    </li>

    <!-- Borrows -->
    <li class="nav-item {{ is_current_role_route('borrows.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dynamic_route('borrows.index') }}">
            <i class="fas fa-fw fa-copy"></i>
            <span>Peminjaman</span>
        </a>
    </li>

    <!-- Returns -->
    <li class="nav-item {{ is_current_role_route('returns.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dynamic_route('returns.index') }}">
            <i class="fas fa-fw fa-paste"></i>
            <span>Pengembalian</span>
        </a>
    </li>

    <!-- Sidebar Toggler -->
    <div class="mt-5 text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>