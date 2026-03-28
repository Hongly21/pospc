<div class="sidebar">
    <div class="d-md-none text-end mb-3">
        <button class="btn btn-sm btn-outline-secondary" onclick="$('.sidebar').removeClass('active')">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-brand">
        <i class="fas fa-layer-group"></i>
        <span>ប្រព័ន្ធ POS</span>
    </div>

    {{-- <div class="menu-header">ទិដ្ឋភាពទូទៅ</div> --}}
    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie icon"></i> ផ្ទាំងគ្រប់គ្រង
        </a>
    @endif

    <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.index') ? 'active' : '' }}">
        <i class="fas fa-cash-register icon text-primary"></i> ផ្ទាំងលក់ (POS)
    </a>

    <a href="{{ route('pos.history') }}" class="{{ request()->routeIs('pos.history') ? 'active' : '' }}">
        <i class="fas fa-history icon"></i> ប្រវត្តិការលក់
    </a>

    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <div class="menu-header">គ្រប់គ្រងស្តុក</div>

        <a href="#" class="dropdown-toggle" onclick="toggleSubmenu(this); return false;"
            aria-expanded="{{ request()->is('products*') || request()->is('categories*') || request()->is('inventory*') ? 'true' : 'false' }}">
            <i class="fas fa-box-open icon"></i> ទំនិញ
            <i class="fas fa-chevron-down arrow-icon"></i>
        </a>

        <div id="productMenu" class="submenu"
            style="display: {{ request()->is('products*') || request()->is('categories*') || request()->is('inventory*') ? 'block' : 'none' }};">
            <a href="{{ url('/categories') }}"
                class="{{ request()->is('categories*') ? 'active' : '' }}">ប្រភេទទំនិញ</a>
            <a href="{{ url('/products') }}" class="{{ request()->is('products*') ? 'active' : '' }}">ទំនិញទាំងអស់</a>
            <a href="{{ url('/inventory') }}"
                class="{{ request()->is('inventory*') ? 'active' : '' }}">កែតម្រូវស្តុក</a>
        </div>

        <a href="{{ url('/purchases') }}" class="{{ request()->is('purchases*') ? 'active' : '' }}">
            <i class="fas fa-truck-loading icon"></i> ការបញ្ជាទិញចូល
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <div class="menu-header">ទំនាក់ទំនង (Contacts)</div>

        <a class="{{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
            <i class="fas fa-users fa-fw icon"></i> អតិថិជន
        </a>

        <a href="{{ url('/suppliers') }}" class="{{ request()->is('suppliers*') ? 'active' : '' }}">
            <i class="fas fa-truck icon"></i> អ្នកផ្គត់ផ្គង់
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin'))
        <div class="menu-header">ហិរញ្ញវត្ថុ និង របាយការណ៍</div>

        <a href="{{ url('/expenses') }}" class="{{ request()->is('expenses*') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-usd icon "></i> កត់ត្រាចំណាយ
        </a>

        <a href="{{ route('reports.sales') }}" class="{{ request()->is('reports/sales*') ? 'active' : '' }}">
            <i class="fas fa-fw fa-chart-area icon "></i> របាយការណ៍លក់
        </a>
    @endif

    @if (auth()->user()->hasRole('Admin'))
        <div class="menu-header">រដ្ឋបាល (Administration)</div>

        <a href="{{ url('/users') }}" class="{{ request()->is('users*') ? 'active' : '' }}">
            <i class="fas fa-users-cog icon"></i> គ្រប់គ្រងបុគ្គលិក
        </a>

        <a href="{{ url('/settings') }}" class="{{ request()->is('settings*') ? 'active' : '' }}">
            <i class="fas fa-cogs icon"></i> ការកំណត់ប្រព័ន្ធ
        </a>
    @endif

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
            title: 'តើអ្នកចង់ចាកចេញទេ?',
            text: "អ្នកនឹងត្រូវចាកចេញពីប្រព័ន្ធ",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ចាកចេញ',
            cancelButtonText: 'បោះបង់'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('logout') }}";
            }
        });
    }
</script>
