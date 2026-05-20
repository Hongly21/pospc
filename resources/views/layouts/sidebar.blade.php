<div id="sidebar" class="sidebar">
    <div class="d-md-none text-end ">
        <button class="btn btn-sm btn-outline-secondary btn-close-slide-mobile" onclick="closeSidebarMobile()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-brand">
        <div class="brand-logo-wrapper">
            <i class="fas fa-layer-group"></i>
            <span class="nav-label">{{ __('POS System') }}</span>
        </div>
        <i class="fas fa-angles-left sidebar-toggle-btn d-none d-md-block" onclick="toggleDesktopSidebar()"
            data-tooltip="{{ __('open_sidebar') }}"></i>
    </div>

    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}"
            data-tooltip="{{ __('Dashboard') }}">
            <i class="fas fa-chart-pie icon"></i> <span class="nav-label">{{ __('Dashboard') }}</span>
        </a>
    @endif

    <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.index') ? 'active' : '' }}"
        data-tooltip="{{ __('POS') }}">
        <i class="fas fa-cash-register icon text-primary"></i> <span class="nav-label">{{ __('POS') }}</span>
    </a>

    <a href="{{ route('pos.history') }}" class="{{ request()->routeIs('pos.history') ? 'active' : '' }}"
        data-tooltip="{{ __('Sales History') }}">
        <i class="fas fa-history icon"></i> <span class="nav-label">{{ __('Sales History') }}</span>
    </a>

    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        @php
            $lowStockCount = \App\Models\Inventory::whereRaw('Quantity <= ReorderLevel')->count();
        @endphp

        <div class="menu-header">{{ __('Stock Management') }}</div>

        <a href="#" class="dropdown-toggle" onclick="toggleSubmenu(this); return false;"
            aria-expanded="{{ request()->is('products*') || request()->is('categories*') || request()->is('inventory*') ? 'true' : 'false' }}">
            <i class="fas fa-box-open icon"></i> <span class="nav-label">{{ __('Products') }}</span>
            <i class="fas fa-chevron-down arrow-icon"></i>
        </a>

        <div id="productMenu" class="submenu {{ request()->is('products*') || request()->is('categories*') || request()->is('inventory*') ? 'show' : '' }}">
            <a href="{{ url('/categories') }}" class="{{ request()->is('categories*') ? 'active' : '' }}">
                <span class="nav-label">{{ __('Categories') }}</span>
            </a>
            <a href="{{ url('/products') }}" class="{{ request()->is('products*') ? 'active' : '' }}">
                <span class="nav-label">{{ __('All Products') }}</span>
            </a>
            <a href="{{ url('/inventory') }}"
                class="{{ request()->is('inventory*') ? 'active' : '' }} position-relative">
                <span class="nav-label">{{ __('Adjust Inventory') }}</span>
                @if ($lowStockCount > 0)
                    <span class="sidebar-badge badge rounded-pill bg-danger">{{ $lowStockCount }}</span>
                @endif
            </a>
        </div>

        <a href="{{ url('/purchases') }}" class="{{ request()->is('purchases*') ? 'active' : '' }}"
            data-tooltip="{{ __('Purchases') }}">
            <i class="fas fa-truck-loading icon"></i> <span class="nav-label">{{ __('Purchases') }}</span>
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <div class="menu-header">{{ __('Contacts') }}</div>

        <a class="{{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"
            data-tooltip="{{ __('Customers') }}">
            <i class="fas fa-users fa-fw icon"></i> <span class="nav-label">{{ __('Customers') }}</span>
        </a>

        <a href="{{ url('/suppliers') }}" class="{{ request()->is('suppliers*') ? 'active' : '' }}"
            data-tooltip="{{ __('Suppliers') }}">
            <i class="fas fa-truck icon"></i> <span class="nav-label">{{ __('Suppliers') }}</span>
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin'))
        <div class="menu-header">{{ __('Finance & Reports') }}</div>

        <a href="{{ url('/expenses') }}" class="{{ request()->is('expenses*') ? 'active' : '' }}"
            data-tooltip="{{ __('Expenses') }}">
            <i class="fas fa-hand-holding-usd icon "></i> <span class="nav-label">{{ __('Expenses') }}</span>
        </a>

        <a href="{{ route('reports.sales') }}" class="{{ request()->is('reports/sales*') ? 'active' : '' }}"
            data-tooltip="{{ __('Sales Reports') }}">
            <i class="fas fa-fw fa-chart-area icon "></i> <span class="nav-label">{{ __('Sales Reports') }}</span>
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin'))
        @php
            // $pendingCount = \App\Models\User::where('Status', 'Pending')->count();
            $pendingCount = \App\Models\User::where('Status', 'Pending')->count();
            $lowStockCount = \App\Models\Inventory::whereRaw('Quantity <= ReorderLevel')->count();
            $totalNotifications = $pendingCount + $lowStockCount;
        @endphp

        <div class="menu-header">{{ __('Administration') }}</div>

        <a href="{{ url('/users') }}" class="{{ request()->is('users*') ? 'active' : '' }} position-relative"
            data-tooltip="{{ __('Manage Staff') }}">
            <i class="fas fa-users-cog icon"></i>
            <span class="nav-label">{{ __('Manage Staff') }}</span>
            @if ($totalNotifications > 0)
                <span class="sidebar-badge badge rounded-pill bg-danger">{{ $totalNotifications }}</span>
            @endif
        </a>
        <a href="{{ route('taxes.index') }}" class="{{ request()->is('taxes*') ? 'active' : '' }}"
            data-tooltip="{{ __('taxes.page_title') }}">
            <i class="fas fa-percentage icon"></i> <span class="nav-label">{{ __('taxes.page_title') }}</span>
        </a>
        <a href="{{ url('/settings') }}" class="{{ request()->is('settings*') ? 'active' : '' }}"
            data-tooltip="{{ __('System Settings') }}">
            <i class="fas fa-cogs icon"></i> <span class="nav-label">{{ __('System Settings') }}</span>
        </a>
    @endif

    {{-- Dark Mode Toggle --}}
    <div class="dark-mode-toggle" onclick="toggleDarkMode()" data-tooltip="{{ __('Dark Mode') }}">
        <span>
            <i class="fas fa-moon icon" id="dark-mode-icon"></i>
            <span class="nav-label" id="dark-mode-label">{{ __('Dark Mode') }}</span>
        </span>
        <div class="theme-switch" id="theme-switch"></div>
    </div>
</div>
