<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'kh' ? 'km' : 'en' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('Uploads/products/Yotta_Icon.png') }}">
    <title>POS - {{ auth()->check() ? auth()->user()->Username : 'Guest' }} -
        {{ auth()->check() ? auth()->user()->role->RoleName ?? 'POS System' : 'POS System' }}</title>

    <!-- Prevent FOUC for Dark Mode -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Vite: Bootstrap (NPM) + Tailwind CSS -->
    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loading-overlay.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
    @stack('styles')

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Global loading overlay (Bootstrap spinner + full-screen backdrop) -->
    <div id="global-loading-overlay" class="global-loading-overlay d-none" aria-hidden="true" aria-live="polite">
        <div class="global-loading-overlay__content">
            <div class="spinner-border text-light global-loading-overlay__spinner" role="status">
                <span class="visually-hidden">{{ __('Loading...') }}</span>
            </div>
        </div>
    </div>

    @include('layouts.sidebar')
    <div class="sidebar-backdrop"></div>

    <div class="main-content">
        <div class="top-navbar no-print">
            <div class="top-navbar-wrapper">
                <!-- Left Side: Toggle & Title -->
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <i class="fas fa-bars sidebar-toggle text-dark fs-5" role="button" aria-label="Toggle Sidebar"></i>
                    <h4 class="m-0 text-dark fw-bold text-truncate" title="@yield('title', __('Dashboard'))">
                        @yield('title', __('Dashboard'))
                    </h4>
                </div>

                <!-- Right Side: Notifications & User Profile -->

                <div class="navbar-actions">
                    <div class="user-control-panel">

                        <!-- Language -->
                        <div class="dropdown">
                            <button class="nav-action-btn" data-bs-toggle="dropdown">
                                <i class="fas fa-globe"></i>
                                <span class="lang-code">
                                    {{ app()->getLocale() == 'kh' ? 'KH' : 'EN' }}
                                </span>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('lang.switch', 'kh') }}">
                                        🇰🇭 Khmer
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">
                                        🇺🇸 English
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Dark Mode -->
                        <button id="theme-switch" onclick="toggleDarkMode()" class="nav-action-btn">
                            <i class="fas fa-moon" id="dark-mode-icon"></i>
                        </button>

                        <!-- Notification -->
                        @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager')))

                            @php
                                $pendingCount = \App\Models\User::where('Status', 'Pending')->count();
                                $lowStockCount = \App\Models\Inventory::whereRaw('Quantity <= ReorderLevel')->count();
                                $totalNotifications = $pendingCount + $lowStockCount;
                            @endphp

                            <div class="dropdown">
                                <button class="nav-action-btn position-relative" data-bs-toggle="dropdown">

                                    <i class="fas fa-bell"></i>

                                    @if ($totalNotifications > 0)
                                        <span class="modern-badge">
                                            {{ $totalNotifications > 99 ? '99+' : $totalNotifications }}
                                        </span>
                                    @endif
                                </button>

                                <!-- KEEP YOUR EXISTING NOTIFICATION MENU HERE -->
                                <ul class="dropdown-menu dropdown-menu-end notification-menu">
                                    <li class="px-3 py-3 border-bottom sticky-top bg-white z-1">
                                        <div class="fw-bold text-dark">{{ __('Notifications') }}</div>
                                        <div class="small text-muted">{{ __('Stay_updated_with_important_alerts') }}
                                        </div>
                                    </li>

                                    @if ($pendingCount > 0)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-start gap-3 px-3 py-3 border-bottom"
                                                href="{{ url('/users') }}?status=Pending">
                                                <div
                                                    class="notification-icon rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="fas fa-user-plus"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="fw-medium text-dark text-truncate mb-1">
                                                        {{ __('Pending_User_Approvals') }}</div>
                                                    <div class="small text-muted text-wrap">{{ $pendingCount }}
                                                        {{ __('pending_approval') }}</div>
                                                </div>
                                            </a>
                                        </li>
                                    @endif

                                    @if ($lowStockCount > 0)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-start gap-3 px-3 py-3"
                                                href="{{ url('/inventory') }}">
                                                <div
                                                    class="notification-icon rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="fw-medium text-dark text-truncate mb-1">
                                                        {{ __('Low_Stock_Alert') }}</div>
                                                    <div class="small text-muted text-wrap">{{ $lowStockCount }}
                                                        {{ __('product_low_on_stock') }}</div>
                                                </div>
                                            </a>
                                        </li>
                                    @endif

                                    @if ($totalNotifications == 0)
                                        <li class="px-3 py-5 text-center text-muted">
                                            <div class="mb-2 opacity-50"><i class="fas fa-bell-slash fa-2x"></i></div>
                                            <div>{{ __('No new notifications') }}</div>
                                        </li>
                                    @endif

                                    <li class="border-top sticky-bottom bg-white z-1">
                                        <a class="dropdown-item text-center py-2 fw-medium text-primary"
                                            href="{{ url('/inventory') }}">
                                            {{ __('View_All') }}
                                        </a>
                                    </li>
                                </ul>

                            </div>

                        @endif

                        <!-- Profile -->
                        <div class="user-profile dropdown">
                            <a href="#" class="profile-trigger" data-bs-toggle="dropdown">
                                @if (Auth::check() && Auth::user()->UserImage)
                                    <img src="{{ str_starts_with(Auth::user()->UserImage, 'http')
                                        ? Auth::user()->UserImage
                                        : asset('storage/' . Auth::user()->UserImage) }}"
                                        class="user-avatar" alt="Profile">
                                @else
                                    <div class="user-avatar-placeholder">
                                        {{ strtoupper(substr(Auth::user()->Username ?? 'U', 0, 1)) }}
                                    </div>
                                @endif

                                <div class="profile-details">
                                    <div class="profile-name">
                                        {{ Auth::user()->Username ?? 'Guest' }}
                                    </div>

                                    <div class="profile-role">
                                        {{ optional(Auth::user()->role)->RoleName ?? 'User' }}
                                    </div>
                                </div>

                                <i class="fas fa-chevron-down profile-arrow"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown-menu">
                                <li class="px-3 py-2 border-bottom mb-2">
                                    <div class="fw-bold text-dark text-truncate">
                                        {{ Auth::check() ? Auth::user()->Username : 'Guest' }}</div>
                                    <div class="small text-muted text-truncate">
                                        {{ Auth::check() ? Auth::user()->Email : '' }}</div>
                                </li>

                                <li class="px-3 pt-2 pb-1">
                                    <small class="text-muted fw-bold text-uppercase section-label">
                                        {{ __('Language') }}
                                    </small>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ app()->getLocale() == 'kh' ? 'active-lang' : '' }}"
                                        href="{{ route('lang.switch', 'kh') }}">
                                        <span>🇰🇭 {{ __('lang.kh') }}</span>
                                        @if (app()->getLocale() == 'kh')
                                            <i class="fas fa-check text-primary"></i>
                                        @endif
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ app()->getLocale() == 'en' ? 'active-lang' : '' }}"
                                        href="{{ route('lang.switch', 'en') }}">
                                        <span>🇺🇸 {{ __('lang.en') }}</span>
                                        @if (app()->getLocale() == 'en')
                                            <i class="fas fa-check text-primary"></i>
                                        @endif
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>

                                @if (Auth::check())
                                    <li>
                                        <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2"
                                            href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-circle text-primary icon-label"></i>
                                            {{ __('My Profile') }}
                                        </a>
                                    </li>

                                    @if (optional(auth()->user()->role)->RoleName === 'Admin')
                                        <li>
                                            <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2 {{ request()->is('settings*') ? 'active' : '' }}"
                                                href="{{ route('settings.index') }}">
                                                <i class="fas fa-cog text-secondary icon-label"></i>
                                                {{ __('Settings') }}
                                            </a>
                                        </li>
                                    @endif

                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>

                                    <li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="m-0">
                                            @csrf
                                            <button type="button"
                                                class="dropdown-item rounded py-2 text-danger fw-bold d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start"
                                                onclick="confirmLogout(event)">
                                                <i class="fas fa-sign-out-alt icon-label"></i> {{ __('Logout') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>

    <!-- Chatbot UI (Floating) -->
    <div id="chat-bubble" class="shadow-lg" role="button" aria-label="Open AI Assistant">
        <i class="fas fa-sms"></i>
    </div>

    <div id="chat-window" class="card shadow-lg d-none">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 fw-bold"><i class="fas fa-robot me-2"></i> {{ __('POS AI Assistant') }}</h6>
            {{-- <i class="fas fa-times text-white"  class="btn-close btn-close-white" id="close-chat" style="cursor: pointer;" aria-label="Close"></i> --}}
            <button type="button" class="btn-close btn-close-white" id="close-chat" aria-label="Close">
                <i class="fa fa-times text-dark"></i>
            </button>
        </div>
        <div class="card-body bg-light chat-window-body" id="chat-content">
            <div class="message-ai mb-3 d-flex flex-column align-items-start chat-ai-message">
                <small class="text-muted ms-1 mb-1 chat-message-label">{{ __('AI Assistant') }}</small>
                <div class="chat-message-bubble bg-white p-2 px-3 rounded-3 shadow-sm border border-light text-dark">
                    {{ __('Hello! I can help you check stock, sales, or answer questions. What do you need?') }}
                </div>
            </div>
        </div>
        <div class="card-footer bg-white p-2">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control form-control-sm border-secondary-subtle"
                    placeholder="{{ __('Type a message...') }}" autocomplete="off">
                <button class="btn btn-primary btn-sm px-3" id="send-chat" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Core Scripts: Bootstrap + jQuery via Vite (must load before page scripts) -->
    @vite(['resources/js/app.js'])

    <script>
        window.appLayoutConfig = {
            messages: {
                aiAssistantLabel: "{{ __('AI Assistant') }}",
                thinking: "{{ __('Thinking...') }}",
                chatbotErrorGeneric: "{{ __('Sorry, I could not process that.') }}",
                chatbotErrorFallback: "{{ __('Sorry, something went wrong. Please try again.') }}"
            },
            routes: {
                chatbot: "{{ route('chatbot.chat') }}"
            },
            sidebar: {
                logoutConfirmTitle: "{{ __('Are you sure you want to logout?') }}",
                logoutConfirmText: "{{ __('You will be logged out of the system.') }}",
                logoutButton: "{{ __('Logout') }}",
                cancelButton: "{{ __('Cancel') }}",
                logoutRoute: "{{ route('logout') }}"
            },
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.app-alert-stack .alert.alert-success').forEach(function(alertEl) {
                window.setTimeout(function() {
                    if (window.bootstrap && typeof window.bootstrap.Alert === 'function') {
                        var bsAlert = window.bootstrap.Alert.getOrCreateInstance(alertEl);
                        bsAlert.close();
                    } else {
                        alertEl.classList.remove('show');
                        alertEl.classList.add('fade');
                        alertEl.remove();
                    }
                }, 4000);
            });
        });
    </script>

    <script src="{{ asset('js/layouts/loading-overlay.js') }}" defer></script>
    <script src="{{ asset('js/layouts/app.js') }}" defer></script>
</body>

</html>
