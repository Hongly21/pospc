<!DOCTYPE html>
<html lang="en">

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
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-hover: #334155;
            --accent-color: #38bdf8;
            --bg-light: #f3f4f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
            font-family: 'Kantumruy Pro', sans-serif;
        }

        /* --- SIDEBAR CONTAINER --- */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: var(--sidebar-bg);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px 15px;
        }

        /* --- BRAND / LOGO --- */
        .sidebar-brand {
            padding: 10px 15px 30px 15px;
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand i {
            color: var(--accent-color);
        }

        /* --- SECTION HEADERS --- */
        .menu-header {
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            margin: 25px 0 10px 15px;
            letter-spacing: 1px;
        }

        /* --- MENU LINKS --- */
        .sidebar a {
            padding: 12px 20px;
            text-decoration: none;
            font-size: 0.95rem;
            color: var(--sidebar-text);
            display: flex;
            align-items: center;
            border-radius: 12px;
            transition: all 0.2s ease;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .sidebar a i.icon {
            width: 25px;
            font-size: 1.1rem;
            margin-right: 10px;
            text-align: center;
        }

        /* HOVER STATE */
        .sidebar a:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
            transform: translateX(5px);
        }

        /* ACTIVE STATE */
        .sidebar a.active {
            background: linear-gradient(135deg, var(--accent-color), #0ea5e9);
            color: #fff;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
        }

        /* --- DROPDOWN SUBMENU --- */
        .submenu {
            padding-left: 10px;
            display: none;
            margin-top: 5px;
            position: relative;
        }

        .submenu::before {
            content: '';
            position: absolute;
            left: 28px;
            top: 0;
            bottom: 15px;
            width: 2px;
            background: #334155;
            border-radius: 2px;
        }

        .submenu a {
            padding: 10px 15px 10px 35px;
            font-size: 0.9rem;
            background: transparent !important;
            color: #94a3b8;
        }

        .submenu a:hover {
            color: #fff;
            transform: none;
        }

        .submenu a.active {
            color: var(--accent-color);
            font-weight: 600;
            box-shadow: none;
            background: none !important;
        }

        .arrow-icon {
            transition: transform 0.3s ease;
            font-size: 0.8em;
            margin-left: auto;
        }

        .dropdown-toggle[aria-expanded="true"] .arrow-icon {
            transform: rotate(180deg);
        }

        /* --- MAIN CONTENT & MOBILE --- */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            transition: all 0.3s;
        }

        .top-navbar {
            background: #fff;
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .sidebar-toggle {
            display: none !important;
            cursor: pointer;
            font-size: 1.5rem;
            margin-right: 15px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                left: 0;
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block !important;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }

        /* Response alert message  for phone */
        @media (max-width: 600px) {
            div.swal2-container {
                width: 100% !important;
                top: 15px !important;
                left: 0 !important;
                right: 0 !important;
                padding: 0 20px !important;
            }

            div.swal2-popup.swal2-toast {
                width: 90% !important;
                /* top: 10px !important; */
                bottom: 25px;
                max-width: 350px !important;
                margin: 0 auto !important;
                padding: 10px 15px !important;
                display: flex !important;
                /* background-color: aquamarine !important; */
                flex-direction: row !important;
                align-items: center !important;
                justify-content: flex-start !important;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            }

            div.swal2-icon {
                border-color: #a5dc86 !important;
                color: #a5dc86 !important;
                min-width: 32px !important;
                width: 32px !important;
                align-items: center !important;
                height: 32px !important;
                /* margin: 0 12px 0 10px !important; */
                border-width: 2px !important;
            }

            div.swal2-icon .swal2-icon-content {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 18px !important;
            }

            div.swal2-success-ring,
            div.swal2-success-fix,
            div.swal2-success-circular-line-left,
            div.swal2-success-circular-line-right {
                display: none !important;
            }

            div.swal2-title {
                font-size: 12px !important;
                text-align: left !important;
                margin: 0 !important;
                flex-grow: 1 !important;
                word-wrap: break-word !important;
            }

            @media (max-width: 600px) {
                .modal-dialog {
                    margin: 0.5rem !important;
                }

                .modal-title {
                    font-size: 1.1rem;
                }
            }

        }
    </style>



    {{-- chartbot style --}}
    <style>
        #chat-bubble {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 60px;
            height: 60px;
            background-color: #007bff;
            /* Matches your primary blue */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s;
        }

        #chat-bubble:hover {
            transform: scale(1.1);
        }

        #chat-window {
            position: fixed;
            bottom: 95px;
            right: 25px;
            width: 350px;
            z-index: 9999;
            border-radius: 15px;
            overflow: hidden;
            border: none;
        }

        .message-user {
            text-align: right;
        }

        .message-user div {
            background-color: #007bff;
            color: white;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    @include('layouts.sidebar')

    <div class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <i class="fas fa-bars sidebar-toggle text-dark " onclick="$('.sidebar').toggleClass('active');"></i>
                <h4 class="m-1 text-dark fw-bold">@yield('title', 'Dashboard')</h4>
            </div>

            <div class="user-info dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                    id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end me-3 d-none d-md-block">
                        <span class="d-block fw-bold text-dark">{{ Auth::user()->Username ?? 'Guest' }}</span>

                        <small class="text-muted text-uppercase" style="font-size: 0.75rem;">
                            {{ Auth::user()->role->RoleName ?? 'Staff' }}
                        </small>
                    </div>
                    @if (Auth::user()->UserImage)
                        <img src="{{ asset('storage/' . Auth::user()->UserImage) }}"
                            class="rounded-circle shadow-sm border border-2 border-white" width="45" height="45"
                            style="object-fit: cover;" alt="Profile Image">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->Username ?? 'User') }}&background=38bdf8&color=fff&bold=true"
                            class="rounded-circle shadow-sm border border-2 border-white" width="45" height="45"
                            alt="Avatar">
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" aria-labelledby="profileDropdown"
                    style="width: 200px;">
                    <li class="px-3 py-2 border-bottom mb-2">
                        <div class="fw-bold text-dark">{{ Auth::user()->Username }}</div>
                        <div class="small text-muted text-truncate">{{ Auth::user()->Email }}</div>
                    </li>

                    <li>
                        <a class="dropdown-item rounded py-2" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-circle me-2 text-primary"></i> ប្រវត្តិរបស់ខ្ញុំ
                        </a>
                    </li>

                    @if (optional(auth()->user()->role)->RoleName === 'Admin')
                        <li>
                            <a class="dropdown-item rounded py-2 {{ request()->is('settings*') ? 'active' : '' }}"
                                href="{{ route('settings.index') }}">
                                <i class="fas fa-cog me-2 text-secondary"></i> ការកំណត់
                            </a>
                        </li>
                    @endif


                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item rounded py-2 text-danger fw-bold" href="#"
                            onclick="confirmLogout(event)">
                            <i class="fas fa-sign-out-alt me-2"></i> ចាកចេញ
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @yield('content')
    </div>

    {{-- chartbot ui --}}

    <div id="chat-bubble" class="shadow-lg">
        <i class="fas fa-robot text-white fa-lg"></i>
    </div>

    <div id="chat-window" class="card shadow-lg d-none">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-robot mr-2"></i> POS AI Assistant</h6>
            <button type="button" class="close text-white" id="close-chat">&times;</button>
        </div>
        <div class="card-body" id="chat-content" style="height: 300px; overflow-y: auto; background-color: #f4f7f6;">
            <div class="message-ai mb-3">
                <small class="text-muted d-block">AI Assistant</small>
                <div class="bg-white p-2 rounded shadow-sm d-inline-block border" style="max-width: 85%;">
                    Hello! I can help you check stock, sales, or answer questions about your system. What do you need?
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control border-0" placeholder="Type a message...">
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

        $('#send-chat').click(function() {
            let message = $('#user-input').val();
            let token = $('meta[name="csrf-token"]').attr(
                'content');

            if (message != "") {
                $('#chat-content').append(`<div class="message-user mb-3 text-right">
            <div class="bg-primary text-white p-2 rounded d-inline-block">${message}</div>
        </div>`);

                $('#user-input').val('');
                $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);

                $.ajax({
                    url: "",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        message: message
                    },
                    success: function(response) {
                        $('#chat-content').append(`<div class="message-ai mb-3">
                    <small class="text-muted d-block">AI Assistant</small>
                    <div class="bg-white p-2 rounded shadow-sm d-inline-block border">${response.reply}</div>
                </div>`);
                        $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);
                    }
                });
            }
        });
    });
</script>
