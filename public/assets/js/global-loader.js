(function () {
    // Helpers
    const $overlay = document.getElementById('global-loading-overlay');
    const $message = document.getElementById('global-loading-message');
    const $spinner = document.getElementById('global-loading-spinner');

    function showOverlay(message) {
        if (!$overlay) return;
        if (message) $message.innerHTML = message;
        $overlay.classList.remove('d-none');
        $overlay.classList.add('show');
        $overlay.setAttribute('aria-hidden', 'false');
        document.body.setAttribute('aria-busy', 'true');
    }

    function hideOverlay() {
        if (!$overlay) return;
        $overlay.classList.remove('show');
        $overlay.classList.add('d-none');
        $overlay.setAttribute('aria-hidden', 'true');
        document.body.removeAttribute('aria-busy');
    }

    // Public API
    window.Loader = {
        show: function (msg) { showOverlay(msg || 'Please wait… Processing'); },
        hide: function () { hideOverlay(); }
    };

    // Attach to normal form submits (non-AJAX)
    document.addEventListener('DOMContentLoaded', function () {
        // delegate submit handler for all forms
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            // if form has attribute data-loader="false" skip loader
            if (form.getAttribute('data-loader') === 'false') return;

            const isAjax = form.getAttribute('data-loader-ajax') === 'true';
            const msg = form.getAttribute('data-loader-message') || form.getAttribute('data-loader-msg') || 'Please wait… Processing';

            // show loader on submit
            showOverlay(msg);

            // make textual inputs readonly (safe — values are submitted)
            const textInputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="tel"], textarea');
            textInputs.forEach(el => {
                el.dataset._prevReadonly = el.readOnly ? '1' : '0';
                el.readOnly = true;
            });

            // disable only submit buttons to prevent double submit
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(btn => {
                btn.dataset._prevDisabled = btn.disabled ? '1' : '0';
                btn.disabled = true;
            });

            // if AJAX form, do not auto hide — expect JS to call Loader.hide()
            if (isAjax) {
                // nothing else, leave overlay shown
                return;
            }

            // For non-AJAX: allow normal form submission to proceed and overlay persists until navigation
            // For robustness, set a small timeout to re-show overlay if some browsers attempt to re-render
            setTimeout(() => {
                // noop
            }, 100);
        }, true /* useCapture so early */);

        // restore state when navigating back or when page is shown
        function restoreState() {
            hideOverlay();

            // restore readonly
            document.querySelectorAll('input[data-_prev-readonly], textarea[data-_prev-readonly]').forEach(el => {
                el.readOnly = el.dataset._prevReadonly === '1';
                delete el.dataset._prevReadonly;
            });

            // restore submit buttons
            document.querySelectorAll('button[data-_prev-disabled], input[type="submit"][data-_prev-disabled]').forEach(btn => {
                btn.disabled = btn.dataset._prevDisabled === '1';
                delete btn.dataset._prevDisabled;
            });
        }

        window.addEventListener('pageshow', restoreState);
        window.addEventListener('load', restoreState);

        // listen to custom events to hide/show (useful for AJAX flows)
        window.addEventListener('loader:show', function (ev) {
            const msg = (ev && ev.detail && ev.detail.message) ? ev.detail.message : null;
            showOverlay(msg);
        });
        window.addEventListener('loader:hide', function () {
            hideOverlay();
        });
    });
})();
