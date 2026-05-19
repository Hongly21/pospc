<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'kh' ? 'km' : 'en' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('Uploads/products/Yotta_Icon.png') }}">
    <title>POS - {{ auth()->check() ? auth()->user()->Username : 'Guest' }} - {{ auth()->check() ? (auth()->user()->role->RoleName ?? 'POS System') : 'POS System' }}</title>

    <!-- Prevent FOUC for Dark Mode -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
    @stack('styles')

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @include('layouts.sidebar')
    <div class="sidebar-backdrop"></div>

    <div class="main-content">
        <div id="global-loader" class="global-loader">
            <div class="spinner-border text-primary global-loader-spinner" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="top-navbar">
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
                    <!-- Notifications -->
                    @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager')))
                        <div class="notification-bell dropdown">
                            <button type="button" class="btn btn-link p-0 text-decoration-none text-dark position-relative" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Notifications') }}">
                                <i class="fa-solid fa-bell fs-5 mt-2 mr-2"></i>
                                @php
                                    $pendingCount = \App\Models\User::where('Status', 'Pending')->count();
                                    $lowStockCount = \App\Models\Inventory::whereRaw('Quantity <= ReorderLevel')->count();
                                    $totalNotifications = $pendingCount + $lowStockCount;
                                @endphp
                                @if ($totalNotifications > 0)
                                    <span class="notification-badge badge rounded-pill bg-danger border border-light shadow-sm position-absolute mt-2 start-100 translate-middle">
                                        {{ $totalNotifications > 99 ? '99+' : $totalNotifications }}
                                    </span>
                                @endif
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 bg-white notification-menu" aria-labelledby="notificationDropdown">
                                <li class="px-3 py-3 border-bottom sticky-top bg-white z-1">
                                    <div class="fw-bold text-dark">{{ __('Notifications') }}</div>
                                    <div class="small text-muted">{{ __('Stay_updated_with_important_alerts') }}</div>
                                </li>

                                @if ($pendingCount > 0)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-start gap-3 px-3 py-3 border-bottom" href="{{ url('/users') }}?status=Pending">
                                            <div class="notification-icon rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0">
                                                <i class="fas fa-user-plus"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="fw-medium text-dark text-truncate mb-1">{{ __('Pending_User_Approvals') }}</div>
                                                <div class="small text-muted text-wrap">{{ $pendingCount }} {{ __('pending_approval') }}</div>
                                            </div>
                                        </a>
                                    </li>
                                @endif

                                @if ($lowStockCount > 0)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-start gap-3 px-3 py-3" href="{{ url('/inventory') }}">
                                            <div class="notification-icon rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="fw-medium text-dark text-truncate mb-1">{{ __('Low_Stock_Alert') }}</div>
                                                <div class="small text-muted text-wrap">{{ $lowStockCount }} {{ __('product_low_on_stock') }}</div>
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
                                    <a class="dropdown-item text-center py-2 fw-medium text-primary" href="{{ url('/inventory') }}">
                                        {{ __('View_All') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <!-- User Profile -->
                    <div class="user-info dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="flex-shrink-0">
                                @if (Auth::check() && Auth::user()->UserImage)
                                    <img src="{{ asset('storage/' . Auth::user()->UserImage) }}" class="rounded-circle shadow-sm border border-2 border-white object-fit-cover" width="42" height="42" alt="Profile">
                                @else
                                    <div class="avatar-initials rounded-circle bg-primary d-flex align-items-center justify-content-center shadow-sm border border-2 border-white text-white fw-bold">
                                        {{ strtoupper(substr(Auth::check() ? (Auth::user()->Username ?? 'U') : 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="d-none d-md-flex flex-column text-start profile-info">
                                <span class="fw-semibold profile-name small">{{ Auth::check() ? Auth::user()->Username : 'Guest' }}</span>
                                <span class="profile-role small">{{ Auth::check() ? optional(Auth::user()->role)->RoleName ?? 'User' : 'Guest' }}</span>
                            </div>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 profile-dropdown-menu" aria-labelledby="profileDropdown">
                            <li class="px-3 py-2 border-bottom mb-2">
                                <div class="fw-bold text-dark text-truncate">{{ Auth::check() ? Auth::user()->Username : 'Guest' }}</div>
                                <div class="small text-muted text-truncate">{{ Auth::check() ? Auth::user()->Email : '' }}</div>
                            </li>

                            <li class="px-3 pt-2 pb-1">
                                <small class="text-muted fw-bold text-uppercase section-label">
                                    {{ __('Language') }}
                                </small>
                            </li>
                            <li>
                                <a class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ app()->getLocale() == 'kh' ? 'active-lang' : '' }}" href="{{ route('lang.switch', 'kh') }}">
                                    <span>🇰🇭 {{ __('lang.kh') }}</span>
                                    @if (app()->getLocale() == 'kh')
                                        <i class="fas fa-check text-primary"></i>
                                    @endif
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ app()->getLocale() == 'en' ? 'active-lang' : '' }}" href="{{ route('lang.switch', 'en') }}">
                                    <span>🇺🇸 {{ __('lang.en') }}</span>
                                    @if (app()->getLocale() == 'en')
                                        <i class="fas fa-check text-primary"></i>
                                    @endif
                                </a>
                            </li>

                            <li><hr class="dropdown-divider my-1"></li>

                            @if (Auth::check())
                            <li>
                                <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-circle text-primary icon-label"></i> {{ __('My Profile') }}

                            @if (optional(auth()->user()->role)->RoleName === 'Admin')
                                <li>
                                    <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2 {{ request()->is('settings*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                        <i class="fas fa-cog text-secondary icon-label"></i> {{ __('Settings') }}
                                    </a>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider my-1"></li>

                            <li>
                                <a class="dropdown-item rounded py-2 text-danger fw-bold d-flex align-items-center gap-2" href="#" onclick="confirmLogout(event)">
                                    <i class="fas fa-sign-out-alt icon-label"></i> {{ __('Logout') }}
                                </a>
                            </li>
                            @endif
                        </ul>
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
                <input type="text" id="user-input" class="form-control form-control-sm border-secondary-subtle" placeholder="{{ __('Type a message...') }}" autocomplete="off">
                <button class="btn btn-primary btn-sm px-3" id="send-chat" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

    <!-- Global Application Scripts -->
    <script>
        // Desktop Sidebar Toggle Logic
        window.toggleDesktopSidebar = function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleIcon = document.querySelector('.sidebar-toggle-btn');

            if (sidebar && mainContent) {
                sidebar.classList.toggle('mini');
                mainContent.classList.toggle('expanded');
                localStorage.setItem(SIDEBAR_STATE_KEY, sidebar.classList.contains('mini') ? 'mini' : 'expanded');

                if (sidebar.classList.contains('mini')) {
                    if (toggleIcon) {
                        toggleIcon.classList.remove('fa-angles-left');
                        toggleIcon.classList.add('fa-angles-right');
                    }
                } else {
                    if (toggleIcon) {
                        toggleIcon.classList.remove('fa-angles-right');
                        toggleIcon.classList.add('fa-angles-left');
                    }
                }
            }
        };

        const SIDEBAR_STATE_KEY = 'sidebarState';

        function applySavedSidebarState() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleIcon = document.querySelector('.sidebar-toggle-btn');
            const savedState = localStorage.getItem(SIDEBAR_STATE_KEY) || 'expanded';

            if (!sidebar || !mainContent) return;

            if (window.innerWidth > 768 && savedState === 'mini') {
                sidebar.classList.add('mini');
                mainContent.classList.add('expanded');
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-angles-left');
                    toggleIcon.classList.add('fa-angles-right');
                }
            } else {
                sidebar.classList.remove('mini');
                mainContent.classList.remove('expanded');
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-angles-right');
                    toggleIcon.classList.add('fa-angles-left');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            applySavedSidebarState();
        });

        // jQuery Logics
        $(document).ready(function() {
            // --- Loader Logic ---
            $(window).on('load', function() {
                $('#global-loader').fadeOut(1000);
            });

            // $(window).on('beforeunload', function() {
            //     $('#global-loader').fadeIn(150);
            // });

            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    $('#global-loader').hide();
                }
            });

            // --- Sidebar Toggle Logic (Mobile & Desktop) ---
            const $sidebar = $('.sidebar');
            const $sidebarBackdrop = $('.sidebar-backdrop');

            $('.sidebar-toggle').on('click', function(e) {
                e.preventDefault();
                if (window.innerWidth <= 768) {
                    $sidebar.toggleClass('active');
                    $sidebarBackdrop.toggleClass('active', $sidebar.hasClass('active'));
                } else {
                    window.toggleDesktopSidebar();
                }
            });

            // Close sidebar on mobile when clicking outside
            $sidebarBackdrop.on('click', function() {
                $sidebar.removeClass('active');
                $sidebarBackdrop.removeClass('active');
            });

            // Close sidebar on mobile when a link is clicked
            $('.sidebar').on('click', 'a', function() {
                if (window.innerWidth <= 768) {
                    $sidebar.removeClass('active');
                    $sidebarBackdrop.removeClass('active');
                }
            });

            // Handle resize events
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth > 768) {
                        $sidebarBackdrop.removeClass('active');
                        $sidebar.removeClass('active'); // Remove mobile class on resize
                    }
                }, 100);
            });

            // --- Chatbot Logic ---
            const $chatWindow = $('#chat-window');
            const $chatContent = $('#chat-content');
            const $userInput = $('#user-input');
            const $sendBtn = $('#send-chat');

            $('#chat-bubble').click(function() {
                $chatWindow.toggleClass('d-none');
                if (!$chatWindow.hasClass('d-none')) {
                    $userInput.focus();
                    scrollToBottom();
                }
            });

            $('#close-chat').click(function() {
                $chatWindow.addClass('d-none');
            });

            function scrollToBottom() {
                $chatContent.scrollTop($chatContent[0].scrollHeight);
            }

            function appendUserMessage(text) {
                const escapedText = $('<div>').text(text).html();
                $chatContent.append(`
                    <div class="message-user mb-3 d-flex flex-column align-items-end">
                        <div class="chat-message-bubble bg-primary text-white p-2 px-3 rounded-3 shadow-sm">
                            ${escapedText}
                        </div>
                    </div>
                `);
                scrollToBottom();
            }

            function appendAiMessage(htmlContent, id = null) {
                const idAttr = id ? `id="${id}"` : '';
                $chatContent.append(`
                    <div ${idAttr} class="message-ai mb-3 d-flex flex-column align-items-start">
                        <small class="text-muted ms-1 mb-1 chat-message-label">{{ __('AI Assistant') }}</small>
                        <div class="chat-message-bubble bg-white p-2 px-3 rounded-3 shadow-sm border border-light text-dark">
                            ${htmlContent}
                        </div>
                    </div>
                `);
                scrollToBottom();
            }

            function sendChat() {
                let message = $userInput.val().trim();
                if (!message) return;

                appendUserMessage(message);
                $userInput.val('');
                $sendBtn.prop('disabled', true);

                let typingId = 'typing-' + Date.now();
                appendAiMessage('<i class="fas fa-circle-notch fa-spin text-primary"></i> {{ __('Thinking...') }}', typingId);

                $.ajax({
                    url: "{{ route('chatbot.chat') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        message: message
                    },
                    success: function(response) {
                        $('#' + typingId).remove();
                        let reply = response.reply || '{{ __('Sorry, I could not process that.') }}';

                        // Basic Markdown Parsing
                        reply = reply.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                        reply = reply.replace(/\*(.*?)\*/g, '<em>$1</em>');
                        reply = reply.replace(/\n/g, '<br>');
                        reply = reply.replace(/^- (.*?)(<br>|$)/gm, '• $1$2');

                        appendAiMessage(reply);
                    },
                    error: function(xhr) {
                        $('#' + typingId).remove();
                        let errMsg = '{{ __('Sorry, something went wrong. Please try again.') }}';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errMsg = xhr.responseJSON.error;
                        }
                        appendAiMessage(`<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ${errMsg}</span>`);
                    },
                    complete: function() {
                        $sendBtn.prop('disabled', false);
                        $userInput.focus();
                    }
                });
            }

            $sendBtn.click(sendChat);
            $userInput.keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    sendChat();
                }
            });
        });
    </script>
</body>
</html>
