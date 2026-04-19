<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'kh' ? 'km' : 'en' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('Uploads/products/Yotta_Icon.png') }}">
    <title>POS - {{ auth()->user()->Username }} - {{ auth()->user()->role->RoleName ?? 'POS System' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidemenu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
    {{-- Prevent flash of wrong theme --}}
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    @include('layouts.sidebar')

    <div class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <i class="fas fa-bars sidebar-toggle text-dark " onclick="$('.sidebar').toggleClass('active');"></i>
                <h4 class="m-1 text-dark fw-bold">@yield('title', __('Dashboard'))</h4>
            </div>

            <div class="user-info dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                    id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end me-3 d-none d-md-block">
                        <span class="d-block fw-bold text-dark">{{ Auth::user()->Username ?? 'Guest' }}</span>
                        <small class="text-muted text-uppercase" style="font-size: 0.75rem;">
                            {{ __(Auth::user()->role->RoleName ?? 'Staff') }}
                        </small>
                    </div>
                    @if (Auth::user()->UserImage)
                        <img src="{{ asset('storage/' . Auth::user()->UserImage) }}"
                            class="rounded-circle shadow-sm border border-2 border-white" width="45"
                            height="45" style="object-fit: cover;" alt="Profile Image">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->Username ?? 'User') }}&background=38bdf8&color=fff&bold=true"
                            class="rounded-circle shadow-sm border border-2 border-white" width="45"
                            height="45" alt="Avatar">
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" aria-labelledby="profileDropdown"
                    style="width: 220px;">

                    <li class="px-3 py-2 border-bottom mb-2">
                        <div class="fw-bold text-dark">{{ Auth::user()->Username }}</div>
                        <div class="small text-muted text-truncate">{{ Auth::user()->Email }}</div>
                    </li>

                    <li class="px-3 pt-1">
                        <small class="text-muted fw-bold"
                            style="font-size: 0.65rem; letter-spacing: 0.5px; text-transform: uppercase;">
                            {{ __('Language') }}
                        </small>
                    </li>
                    <li>
                        <a class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ app()->getLocale() == 'kh' ? 'bg-light text-primary fw-bold' : '' }}"
                            href="{{ route('lang.switch', 'kh') }}">
                            <span>🇰🇭 ភាសាខ្មែរ</span>
                            @if (app()->getLocale() == 'kh')
                                <i class="fas fa-check fa-xs"></i>
                            @endif
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ app()->getLocale() == 'en' ? 'bg-light text-primary fw-bold' : '' }}"
                            href="{{ route('lang.switch', 'en') }}">
                            <span>🇺🇸 English</span>
                            @if (app()->getLocale() == 'en')
                                <i class="fas fa-check fa-xs"></i>
                            @endif
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item rounded py-2" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-circle me-2 text-primary"></i> {{ __('My Profile') }}
                        </a>
                    </li>

                    @if (optional(auth()->user()->role)->RoleName === 'Admin')
                        <li>
                            <a class="dropdown-item rounded py-2 {{ request()->is('settings*') ? 'active' : '' }}"
                                href="{{ route('settings.index') }}">
                                <i class="fas fa-cog me-2 text-secondary"></i> {{ __('Settings') }}
                            </a>
                        </li>
                    @endif

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item rounded py-2 text-danger fw-bold" href="#"
                            onclick="confirmLogout(event)">
                            <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @yield('content')
    </div>

    {{-- chatbot ui --}}
    <div id="chat-bubble" class="shadow-lg">
        <i class="fas fa-robot text-white fa-lg"></i>
    </div>

    <div id="chat-window" class="card shadow-lg d-none">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-robot mr-2"></i> {{ __('POS AI Assistant') }}</h6>
            <button type="button" class="close text-white" id="close-chat">&times;</button>
        </div>
        <div class="card-body" id="chat-content" style="height: 300px; overflow-y: auto; background-color: #f4f7f6;">
            <div class="message-ai mb-3">
                <small class="text-muted d-block">{{ __('AI Assistant') }}</small>
                <div class="bg-white p-2 rounded shadow-sm d-inline-block border" style="max-width: 85%;">
                    {{ __('Hello! I can help you check stock, sales, or answer questions. What do you need?') }}
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control border-0"
                    placeholder="{{ __('Type a message...') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" id="send-chat">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    $(document).ready(function() {
        $('#chat-bubble').click(function() {
            $('#chat-window').toggleClass('d-none');
        });

        $('#close-chat').click(function() {
            $('#chat-window').addClass('d-none');
        });

        function sendChat() {
            let message = $('#user-input').val().trim();
            if (!message) return;

            // Show user message
            $('#chat-content').append(`<div class="message-user mb-3 text-right">
                <div class="bg-primary text-white p-2 rounded d-inline-block" style="max-width:85%;">${$('<div>').text(message).html()}</div>
            </div>`);
            $('#user-input').val('');
            $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);

            // Show typing indicator
            let typingId = 'typing-' + Date.now();
            $('#chat-content').append(`<div id="${typingId}" class="message-ai mb-3">
                <small class="text-muted d-block">{{ __('AI Assistant') }}</small>
                <div class="bg-white p-2 rounded shadow-sm d-inline-block border">
                    <i class="fas fa-circle-notch fa-spin"></i> {{ __('Thinking...') }}
                </div>
            </div>`);
            $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);
            $('#send-chat').prop('disabled', true);

            $.ajax({
                url: "{{ route('chatbot.chat') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: message
                },
                success: function(response) {
                    $('#' + typingId).remove();
                    // Convert markdown-like formatting
                    let reply = response.reply || '{{ __('Sorry, I could not process that.') }}';
                    reply = reply.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    reply = reply.replace(/\*(.*?)\*/g, '<em>$1</em>');
                    reply = reply.replace(/\n/g, '<br>');
                    reply = reply.replace(/^- (.*?)(<br>|$)/gm, '• $1$2');

                    $('#chat-content').append(`<div class="message-ai mb-3">
                        <small class="text-muted d-block">{{ __('AI Assistant') }}</small>
                        <div class="bg-white p-2 rounded shadow-sm d-inline-block border" style="max-width:85%;">${reply}</div>
                    </div>`);
                    $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);
                },
                error: function(xhr) {
                    $('#' + typingId).remove();
                    let errMsg = '{{ __('Sorry, something went wrong. Please try again.') }}';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errMsg = xhr.responseJSON.error;
                    }
                    $('#chat-content').append(`<div class="message-ai mb-3">
                        <small class="text-muted d-block">{{ __('AI Assistant') }}</small>
                        <div class="bg-white p-2 rounded shadow-sm d-inline-block border border-danger text-danger" style="max-width:85%;">${errMsg}</div>
                    </div>`);
                    $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);
                },
                complete: function() {
                    $('#send-chat').prop('disabled', false);
                    $('#user-input').focus();
                }
            });
        }

        $('#send-chat').click(sendChat);

        $('#user-input').keypress(function(e) {
            if (e.which === 13) sendChat();
        });
    });
</script>
