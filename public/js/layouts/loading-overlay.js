/**
 * Global loading overlay — page navigation, form submits, and jQuery AJAX.
 * Manual control: window.AppLoading.show() / window.AppLoading.hide()
 */
(function() {
    'use strict';

    var OVERLAY_ID = 'global-loading-overlay';
    var NO_LOADING_ATTR = 'data-no-loading';

    var state = {
        ajaxActive: false,
        navigation: false,
        formSubmit: false,
        manual: 0
    };

    function getOverlay() {
        return document.getElementById(OVERLAY_ID);
    }

    function isOverlayVisible() {
        var overlay = getOverlay();
        return overlay && !overlay.classList.contains('d-none');
    }

    function shouldShow() {
        return state.ajaxActive || state.navigation || state.formSubmit || state.manual > 0;
    }

    function showOverlay() {
        var overlay = getOverlay();
        if (!overlay) return;
        overlay.classList.remove('d-none');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('global-loading-active');
    }

    function hideOverlay() {
        var overlay = getOverlay();
        if (!overlay) return;
        overlay.classList.add('d-none');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('global-loading-active');
    }

    function syncOverlay() {
        if (shouldShow()) {
            showOverlay();
        } else {
            hideOverlay();
        }
    }

    function isInternalNavigationLink(link) {
        if (!link || link.tagName !== 'A') return false;
        if (link.hasAttribute(NO_LOADING_ATTR)) return false;

        var href = link.getAttribute('href');
        if (!href || href === '#' || href.charAt(0) === '#') return false;

        var hrefLower = href.toLowerCase();
        if (hrefLower.indexOf('javascript:') === 0 || hrefLower.indexOf('mailto:') === 0 || hrefLower.indexOf('tel:') === 0) {
            return false;
        }

        if (link.target && link.target.toLowerCase() === '_blank') return false;
        if (link.hasAttribute('download')) return false;

        try {
            var url = new URL(href, window.location.href);
            return url.origin === window.location.origin;
        } catch (error) {
            return false;
        }
    }

    function bindEvents() {
        window.addEventListener('beforeunload', function() {
            state.navigation = true;
            showOverlay();
        });

        document.addEventListener('click', function(event) {
            var link = event.target.closest('a');
            if (!link || !isInternalNavigationLink(link)) return;
            if (event.defaultPrevented) return;
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

            state.navigation = true;
            syncOverlay();
        });

        document.addEventListener('submit', function(event) {
            var form = event.target;
            if (!form || form.tagName !== 'FORM') return;
            if (event.defaultPrevented) return;
            if (form.hasAttribute(NO_LOADING_ATTR)) return;

            var target = (form.target || '_self').toLowerCase();
            if (target !== '_self' && target !== '') return;

            state.formSubmit = true;
            syncOverlay();
        });

        if (typeof window.jQuery !== 'undefined') {
            window.jQuery(document).ajaxStart(function() {
                state.ajaxActive = true;
                syncOverlay();
            });

            window.jQuery(document).ajaxStop(function() {
                state.ajaxActive = false;
                syncOverlay();
            });
        }
    }

    window.AppLoading = {
        show: function() {
            state.manual += 1;
            syncOverlay();
        },
        hide: function() {
            state.manual = Math.max(0, state.manual - 1);
            syncOverlay();
        },
        isVisible: isOverlayVisible
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindEvents);
    } else {
        bindEvents();
    }
})();
