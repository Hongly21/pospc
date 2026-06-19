(function() {
    const config = window.appLayoutConfig || {};
    const messages = config.messages || {};
    const route = config.routes?.chatbot || '';
    const csrfToken = config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const SIDEBAR_STATE_KEY = 'sidebarState';

    function applySavedSidebarState() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const toggleIcon = document.querySelector('.sidebar-toggle-btn');
        const savedState = localStorage.getItem(SIDEBAR_STATE_KEY) || 'expanded';

        if (!sidebar || !mainContent) return;

        if (window.innerWidth > 1024 && savedState === 'mini') {
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

    $(document).ready(function() {
        function adjustContentWrapperHeight() {
            const topNavbar = document.querySelector('.top-navbar');
            const contentWrapper = document.querySelector('.content-wrapper');
            const mainContent = document.querySelector('.main-content');
            if (!contentWrapper) return;

            const navbarHeight = topNavbar ? topNavbar.getBoundingClientRect().height : 0;
            const navbarMarginBottom = topNavbar ? parseFloat(getComputedStyle(topNavbar).marginBottom || '0') : 0;
            const mainContentStyle = mainContent ? getComputedStyle(mainContent) : null;
            const mainContentPaddingTop = mainContentStyle ? parseFloat(mainContentStyle.paddingTop || '0') : 0;
            const mainContentPaddingBottom = mainContentStyle ? parseFloat(mainContentStyle.paddingBottom || '0') : 0;
            const totalOffset = navbarHeight + navbarMarginBottom + mainContentPaddingTop + mainContentPaddingBottom;
            contentWrapper.style.minHeight = `calc(100vh - ${totalOffset}px)`;
        }

        adjustContentWrapperHeight();
        window.addEventListener('resize', adjustContentWrapperHeight);

        const $sidebar = $('.sidebar');
        const $sidebarBackdrop = $('.sidebar-backdrop');

        $('.sidebar-toggle').on('click', function(e) {
            e.preventDefault();
            if (window.innerWidth <= 1024) {
                $sidebar.toggleClass('active');
                $sidebarBackdrop.toggleClass('active', $sidebar.hasClass('active'));
            } else {
                window.toggleDesktopSidebar();
            }
        });

        $sidebarBackdrop.on('click', function() {
            $sidebar.removeClass('active');
            $sidebarBackdrop.removeClass('active');
        });

        // $('.sidebar').on('click', 'a', function() {
        //     if (window.innerWidth <= 1024) {
        //         $sidebar.removeClass('active');
        //         $sidebarBackdrop.removeClass('active');
        //     }
        // });

        $('.sidebar').on('click', 'a', function(e) {
            // Prevent the sidebar from closing if the clicked link is a dropdown toggle
            if ($(this).hasClass('dropdown-toggle')) {
                return;
            }

            if (window.innerWidth <= 1024) {
                $sidebar.removeClass('active');
                $sidebarBackdrop.removeClass('active');
            }
        });

        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 1024) {
                    $sidebarBackdrop.removeClass('active');
                    $sidebar.removeClass('active');
                }
            }, 100);
        });

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
                    <small class="text-muted ms-1 mb-1 chat-message-label">${messages.aiAssistantLabel || 'AI Assistant'}</small>
                    <div class="chat-message-bubble bg-white p-2 px-3 rounded-3 shadow-sm border border-light text-dark">
                        ${htmlContent}
                    </div>
                </div>
            `);
            scrollToBottom();
        }

        function sendChat() {
            const message = $userInput.val().trim();
            if (!message) return;

            appendUserMessage(message);
            $userInput.val('');
            $sendBtn.prop('disabled', true);

            const typingId = 'typing-' + Date.now();
            appendAiMessage(`<i class="fas fa-circle-notch fa-spin text-primary"></i> ${messages.thinking || 'Thinking...'}`, typingId);

            $.ajax({
                url: route,
                method: 'POST',
                data: { _token: csrfToken, message },
                success(response) {
                    $('#' + typingId).remove();
                    let reply = response.reply || messages.chatbotErrorGeneric || 'Sorry, I could not process that.';
                    reply = reply.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    reply = reply.replace(/\*(.*?)\*/g, '<em>$1</em>');
                    reply = reply.replace(/\n/g, '<br>');
                    reply = reply.replace(/^- (.*?)(<br>|$)/gm, '• $1$2');
                    appendAiMessage(reply);
                },
                error(xhr) {
                    $('#' + typingId).remove();
                    let errMsg = messages.chatbotErrorFallback || 'Sorry, something went wrong. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errMsg = xhr.responseJSON.error;
                    }
                    appendAiMessage(`<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ${errMsg}</span>`);
                },
                complete() {
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
})();

function isCompactTabletSidebar() {
    return window.matchMedia('(max-width: 1024px)').matches || document.querySelector('.sidebar')?.classList.contains('mini');
}

function closeSidebarMobile() {
    $('.sidebar').removeClass('active');
    $('.sidebar-backdrop').removeClass('active');
}

function toggleSubmenu(element) {
    let submenu = document.getElementById('productMenu');
    let isExpanded = element.getAttribute('aria-expanded') === 'true';

    element.setAttribute('aria-expanded', String(!isExpanded));

    if (isCompactTabletSidebar()) {
        submenu.classList.toggle('show', !isExpanded);
        submenu.classList.toggle('compact-open', !isExpanded);
        return;
    }

    $(submenu).stop(true, true).slideToggle(350, 'swing');
}

function confirmLogout(event) {
    event.preventDefault();
    const layoutConfig = window.appLayoutConfig || {};
    const sidebarLabels = layoutConfig.sidebar || {};

    Swal.fire({
        title: sidebarLabels.logoutConfirmTitle || 'Are you sure you want to logout?',
        text: sidebarLabels.logoutConfirmText || 'You will be logged out of the system.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: sidebarLabels.logoutButton || 'Logout',
        cancelButtonText: sidebarLabels.cancelButton || 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.submit();
            } else {
                window.location.href = sidebarLabels.logoutRoute || '/logout';
            }
        }
    });
}

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
    if (!icon || !toggle) return;
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

(function() {
    var theme = localStorage.getItem('theme') || 'light';
    updateDarkModeUI(theme);
})();

document.addEventListener('click', function(e) {
    if (!isCompactTabletSidebar()) return;
    const productMenu = document.getElementById('productMenu');
    const trigger = document.querySelector('.sidebar .dropdown-toggle');
    if (!productMenu || !trigger) return;
    if (trigger.contains(e.target) || productMenu.contains(e.target)) return;
    productMenu.classList.remove('compact-open');
    productMenu.classList.remove('show');
    trigger.setAttribute('aria-expanded', 'false');
});

window.addEventListener('resize', function() {
    const productMenu = document.getElementById('productMenu');
    if (!productMenu || isCompactTabletSidebar()) return;
    productMenu.classList.remove('compact-open');
    productMenu.classList.remove('show');
});
