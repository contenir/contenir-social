/*
 * contenir/contenir-social: "Copy link" and "Share…" for share buttons.
 *
 * Both buttons are written hidden. This shows "Copy link" where the browser
 * can copy, and "Share…" only where it has a share sheet (navigator.share),
 * which is mostly phones. A copy shows "Copied" beside the button for two
 * seconds, and announces it. It runs once however often it is included, and
 * reads only the buttons' own data attributes: nothing is sent anywhere.
 */
(() => {
    'use strict';

    if (window.contenirShare) {
        return;
    }
    window.contenirShare = true;

    const canShare = typeof navigator.share === 'function';

    const show = (selector) => {
        document.querySelectorAll(selector).forEach((button) => {
            if (button.parentElement) {
                button.parentElement.hidden = false;
            }
        });
    };

    const reveal = () => {
        show('[data-social-copy]');
        if (canShare) {
            show('[data-social-share]');
        }
    };

    const copied = (button) => {
        const status = button.parentElement && button.parentElement.querySelector('[data-social-copied]');
        if (!status) {
            return;
        }
        status.textContent = status.getAttribute('data-social-copied');
        clearTimeout(status.contenirTimer);
        status.contenirTimer = setTimeout(() => {
            status.textContent = '';
        }, 2000);
    };

    const copyByCommand = (text) => {
        const area = document.createElement('textarea');
        area.value = text;
        area.setAttribute('readonly', '');
        area.style.position = 'fixed';
        area.style.opacity = '0';
        document.body.appendChild(area);
        area.select();
        let done = false;
        try {
            done = document.execCommand('copy');
        } catch (error) {
            done = false;
        }
        area.remove();

        return done;
    };

    const copy = (button) => {
        const text = button.getAttribute('data-social-copy') || '';
        const fallback = () => {
            if (copyByCommand(text)) {
                copied(button);
            }
        };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => copied(button), fallback);
            return;
        }
        fallback();
    };

    const share = (button) => {
        const data = { url: button.getAttribute('data-social-share') || '' };
        const title = button.getAttribute('data-social-title');
        if (title) {
            data.title = title;
        }
        navigator.share(data).catch(() => {});
    };

    document.addEventListener('click', (event) => {
        const button = event.target instanceof Element
            ? event.target.closest('[data-social-copy], [data-social-share]')
            : null;
        if (!button) {
            return;
        }
        event.preventDefault();
        if (button.hasAttribute('data-social-copy')) {
            copy(button);
        } else if (canShare) {
            share(button);
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', reveal);
    } else {
        reveal();
    }
})();
