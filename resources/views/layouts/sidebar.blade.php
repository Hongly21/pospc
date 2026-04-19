<div class="sidebar">
    <div class="d-md-none text-end mb-3">
        <button class="btn btn-sm btn-outline-secondary" onclick="$('.sidebar').removeClass('active')">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-brand">
        <i class="fas fa-layer-group"></i>
        <span>{{ __('POS System') }}</span>
    </div>

    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie icon"></i> {{ __('Dashboard') }}
        </a>
    @endif

    <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.index') ? 'active' : '' }}">
        <i class="fas fa-cash-register icon text-primary"></i> {{ __('POS') }}
    </a>

    <a href="{{ route('pos.history') }}" class="{{ request()->routeIs('pos.history') ? 'active' : '' }}">
        <i class="fas fa-history icon"></i> {{ __('Sales History') }}
    </a>

    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <div class="menu-header">{{ __('Stock Management') }}</div>

        <a href="#" class="dropdown-toggle" onclick="toggleSubmenu(this); return false;"
            aria-expanded="{{ request()->is('products*') || request()->is('categories*') || request()->is('inventory*') ? 'true' : 'false' }}">
            <i class="fas fa-box-open icon"></i> {{ __('Products') }}
            <i class="fas fa-chevron-down arrow-icon"></i>
        </a>

        <div id="productMenu" class="submenu"
            style="display: {{ request()->is('products*') || request()->is('categories*') || request()->is('inventory*') ? 'block' : 'none' }};">
            <a href="{{ url('/categories') }}" class="{{ request()->is('categories*') ? 'active' : '' }}">
                {{ __('Categories') }}
            </a>
            <a href="{{ url('/products') }}" class="{{ request()->is('products*') ? 'active' : '' }}">
                {{ __('All Products') }}
            </a>
            <a href="{{ url('/inventory') }}" class="{{ request()->is('inventory*') ? 'active' : '' }}">
                {{ __('Adjust Inventory') }}
            </a>
        </div>

        <a href="{{ url('/purchases') }}" class="{{ request()->is('purchases*') ? 'active' : '' }}">
            <i class="fas fa-truck-loading icon"></i> {{ __('Purchases') }}
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <div class="menu-header">{{ __('Contacts') }}</div>

        <a class="{{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
            <i class="fas fa-users fa-fw icon"></i> {{ __('Customers') }}
        </a>

        <a href="{{ url('/suppliers') }}" class="{{ request()->is('suppliers*') ? 'active' : '' }}">
            <i class="fas fa-truck icon"></i> {{ __('Suppliers') }}
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin'))
        <div class="menu-header">{{ __('Finance & Reports') }}</div>

        <a href="{{ url('/expenses') }}" class="{{ request()->is('expenses*') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-usd icon "></i> {{ __('Expenses') }}
        </a>

        <a href="{{ route('reports.sales') }}" class="{{ request()->is('reports/sales*') ? 'active' : '' }}">
            <i class="fas fa-fw fa-chart-area icon "></i> {{ __('Sales Reports') }}
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin'))
        <div class="menu-header">{{ __('Administration') }}</div>

        <a href="{{ url('/users') }}" class="{{ request()->is('users*') ? 'active' : '' }}">
            <i class="fas fa-users-cog icon"></i> {{ __('Manage Staff') }}
        </a>

        <a href="{{ url('/settings') }}" class="{{ request()->is('settings*') ? 'active' : '' }}">
            <i class="fas fa-cogs icon"></i> {{ __('System Settings') }}
        </a>
    @endif

    {{-- Dark Mode Toggle --}}
    <div class="dark-mode-toggle" onclick="toggleDarkMode()">
        <span>
            <i class="fas fa-moon icon" id="dark-mode-icon"></i>
            <span id="dark-mode-label">{{ __('Dark Mode') }}</span>
        </span>
        <div class="theme-switch" id="theme-switch"></div>
    </div>
</div>

<script>
    function toggleSubmenu(element) {
        let submenu = document.getElementById('productMenu');
        let isExpanded = element.getAttribute('aria-expanded') === 'true';
        element.setAttribute('aria-expanded', !isExpanded);
        $(submenu).slideToggle(200);
    }

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: "{{ __('Are you sure you want to logout?') }}",
            text: "{{ __('You will be logged out of the system.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Logout') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                // It is safer to use a form for logout in Laravel (POST request)
                // But for now, using your existing logic:
                window.location.href = "{{ route('logout') }}";
            }
        });
    }

    // Dark Mode
    function toggleDarkMode() {
        var html = document.documentElement;
        var current = html.getAttribute('data-theme');
        var next = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        updateDarkModeUI(next);
    }

    function updateDarkModeUI(theme) {
        var icon = document.getElementById('dark-mode-icon');
        var toggle = document.getElementById('theme-switch');
        if (theme === 'dark') {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            toggle.classList.add('active');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            toggle.classList.remove('active');
        }
    }

    // Initialize dark mode UI on load
    (function() {
        var theme = localStorage.getItem('theme') || 'light';
        updateDarkModeUI(theme);
    })();
</script>
