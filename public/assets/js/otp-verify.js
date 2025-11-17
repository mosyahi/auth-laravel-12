// public/assets/js/otp-verify.js
(function () {
    'use strict';

    /* ===== Helpers ===== */
    function qs(id) { return document.getElementById(id); }

    function getCsrfToken() {
        // prefer hidden token inside form, otherwise meta tag
        const tokenInput = document.querySelector('#otp-form input[name="_token"]');
        if (tokenInput) return tokenInput.value;
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showAlert(type, message) {
        const area = qs('otp-alerts');
        if (!area) return;
        area.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    }

    function clearAlerts() {
        const area = qs('otp-alerts');
        if (!area) return;
        area.innerHTML = '';
    }

    function showLocalSpinner(btn, spinner, textEl, text) {
        if (btn) btn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
        if (textEl) textEl.textContent = text || textEl.textContent;
    }

    function hideLocalSpinner(btn, spinner, textEl, defaultText) {
        if (btn) btn.disabled = false;
        if (spinner) spinner.classList.add('d-none');
        if (textEl) textEl.textContent = defaultText || textEl.textContent;
    }

    /* restore interactive UI no matter what (call in finally) */
    function restoreInteractiveState() {
        try {
            // local submit
            const submitBtn = qs('otp-submit');
            const spinner = qs('otp-submit-spinner');
            const submitText = qs('otp-submit-text');
            if (submitBtn) submitBtn.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            if (submitText) submitText.textContent = 'Confirm';

            // resend
            const resend = qs('otp-resend');
            if (resend) {
                resend.disabled = false;
                if (resend.dataset._origText) {
                    resend.textContent = resend.dataset._origText;
                    delete resend.dataset._origText;
                }
            }

            // allow typing again
            document.querySelectorAll('.otp-digit').forEach(el => {
                try { el.readOnly = false; } catch (e) {}
                el.style.pointerEvents = '';
            });

            // hide overlay fallback
            const overlay = document.getElementById('global-loading-overlay');
            if (overlay) {
                overlay.classList.remove('show');
                overlay.classList.add('d-none');
                overlay.setAttribute('aria-hidden', 'true');
            }

            // remove busy flag
            try { document.body.removeAttribute('aria-busy'); } catch (e) {}

            // focus first empty digit
            for (let i = 1; i <= 6; i++) {
                const el = qs('digit' + i + '-input');
                if (el && !el.value) { el.focus(); break; }
            }
        } catch (err) {
            console.error('restoreInteractiveState error', err);
        }
    }

    /* ===== Digit handlers ===== */
    window.onDigitInput = function (e) {
        const input = e.target;
        const idx = Number(input.dataset.index || 0);
        input.value = (input.value || '').replace(/[^0-9]/g, '').slice(0, 1);
        input.classList.remove('otp-error');

        if (input.value && idx < 6) {
            qs('digit' + (idx + 1) + '-input')?.focus();
        }

        fillHiddenOtp();
    };

    window.onDigitKeyDown = function (e) {
        const input = e.target;
        const idx = Number(input.dataset.index || 0);
        const key = e.key;

        if (key === 'Backspace') {
            if (!input.value && idx > 1) {
                qs('digit' + (idx - 1) + '-input')?.focus();
            }
            return;
        }

        if (key.length === 1 && !/[0-9]/.test(key)) {
            e.preventDefault();
        }
    };

    function fillHiddenOtp() {
        let otp = '';
        for (let i = 1; i <= 6; i++) {
            otp += qs('digit' + i + '-input')?.value || '';
        }
        const hidden = qs('otp-hidden');
        if (hidden) hidden.value = otp;
        return otp;
    }

    /* ===== Main: verify & resend ===== */
    document.addEventListener('DOMContentLoaded', function () {
        qs('digit1-input')?.focus();

        const otpForm = qs('otp-form');
        if (otpForm) {
            otpForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                clearAlerts();

                const otpValue = fillHiddenOtp();
                if (otpValue.length < 6) {
                    showAlert('danger', 'Mohon masukkan 6 digit kode OTP.');
                    for (let i = 1; i <= 6; i++) {
                        const el = qs('digit' + i + '-input');
                        if (el && !el.value) { el.classList.add('otp-error'); el.focus(); break; }
                    }
                    return;
                }

                const submitBtn = qs('otp-submit');
                const spinner = qs('otp-submit-spinner');
                const submitText = qs('otp-submit-text');

                // show local spinner and global loader (if present)
                showLocalSpinner(submitBtn, spinner, submitText, 'Verifying...');
                if (window.Loader && typeof window.Loader.show === 'function') {
                    try { window.Loader.show('Verifying OTP...'); } catch (_) {}
                }

                // make inputs readonly to avoid accidental edits while processing (readonly still submits)
                document.querySelectorAll('.otp-digit').forEach(el => { try { el.readOnly = true; } catch (e) {} });

                try {
                    const url = otpForm.getAttribute('action') || otpForm.dataset.url || '/otp/verify';
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({ otp: otpValue })
                    });

                    if (res.ok) {
                        const data = await res.json().catch(() => null);
                        if (data && data.success) {
                            // redirect (navigation will hide overlay)
                            const redirectUrl = (data.redirect) ? data.redirect : (otpForm.dataset.redirect || '/home');
                            window.location.href = redirectUrl;
                            return;
                        } else {
                            showAlert('danger', (data && data.message) ? data.message : 'Kode OTP tidak sesuai.');
                            // mark all digits error
                            document.querySelectorAll('.otp-digit').forEach(el => el.classList.add('otp-error'));
                        }
                    } else if (res.status === 422) {
                        const err = await res.json().catch(() => null);
                        if (err && err.errors) {
                            showAlert('danger', Object.values(err.errors).flat().join('<br>'));
                            document.querySelectorAll('.otp-digit').forEach(el => el.classList.add('otp-error'));
                        } else {
                            showAlert('danger', 'Validation failed.');
                        }
                    } else if (res.status === 401 || res.status === 419) {
                        showAlert('danger', 'Sesi kadaluarsa. Silakan login ulang.');
                        setTimeout(() => { window.location.href = (otpForm.dataset.login || '/'); }, 1400);
                    } else {
                        const text = await res.text().catch(() => '');
                        showAlert('danger', 'Terjadi kesalahan server. ' + (text || ''));
                    }
                } catch (err) {
                    console.error(err);
                    showAlert('danger', 'Tidak dapat menghubungi server. Periksa koneksi Anda.');
                } finally {
                    // ALWAYS restore UI so user can type again
                    restoreInteractiveState();

                    // hide global loader if available
                    if (window.Loader && typeof window.Loader.hide === 'function') {
                        try { window.Loader.hide(); } catch (_) {}
                    }
                }
            });
        }

        const resendBtn = qs('otp-resend');
        if (resendBtn) {
            resendBtn.addEventListener('click', async function (e) {
                e.preventDefault();
                clearAlerts();

                const btn = this;
                const url = btn.getAttribute('data-url') || '/otp/resend';

                btn.disabled = true;
                btn.dataset._origText = btn.textContent || 'Resend';
                btn.textContent = 'Sending...';

                if (window.Loader && typeof window.Loader.show === 'function') {
                    try { window.Loader.show('Sending OTP...'); } catch (_) {}
                }

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({})
                    });

                    if (res.ok) {
                        const data = await res.json().catch(() => null);
                        if (data && data.success) {
                            showAlert('success', data.message || 'OTP baru telah dikirim.');
                            document.querySelectorAll('.otp-digit').forEach(el => el.value = '');
                            qs('digit1-input')?.focus();
                        } else {
                            showAlert('danger', (data && data.message) ? data.message : 'Gagal mengirim ulang OTP.');
                        }
                    } else if (res.status === 401 || res.status === 419) {
                        showAlert('danger', 'Sesi kadaluarsa. Silakan login ulang.');
                        setTimeout(() => { window.location.href = (otpForm && otpForm.dataset.login) ? otpForm.dataset.login : '/'; }, 1400);
                    } else {
                        const text = await res.text().catch(() => '');
                        showAlert('danger', 'Terjadi kesalahan: ' + (text || ''));
                    }
                } catch (err) {
                    console.error(err);
                    showAlert('danger', 'Tidak dapat menghubungi server. Periksa koneksi Anda.');
                } finally {
                    // restore resend button + hide loader
                    btn.disabled = false;
                    btn.textContent = btn.dataset._origText || "Didn't receive a code? Resend";
                    if (window.Loader && typeof window.Loader.hide === 'function') {
                        try { window.Loader.hide(); } catch (_) {}
                    }
                    // ensure inputs writable
                    restoreInteractiveState();
                }
            });
        }
    });

})();
