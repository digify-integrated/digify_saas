import { showNotification } from '../modules/notifications.js';

export function copyToClipboard(elementID) {
    const text = document.getElementById(elementID)?.textContent || '';
    if (!text) {
        showNotification('Copy Error', 'No text to copy', 'error');
        return;
    }

    navigator.clipboard.writeText(text)
        .then(() => showNotification('Success', 'Copied to clipboard', 'success'))
        .catch(() => showNotification('Error', 'Failed to copy', 'error'));
}

export function resetForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    $(form).find('.select2').val('').trigger('change');
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.reset();
}

export function getDeviceInfo() {
    const userAgent = navigator.userAgent;
    const deviceMap = {
        Android: /Android/i,
        iOS: /iPhone|iPad|iPod/i,
        Windows: /Windows NT/i,
        MacOS: /Mac OS/i,
        Linux: /Linux/i
    };

    const browserMap = {
        Firefox: /Firefox/i,
        Opera: /OPR|Opera/i,
        Chrome: /Chrome/i,
        Safari: /Safari/i,
        InternetExplorer: /MSIE|Trident/i
    };

    const device = Object.keys(deviceMap).find((key) => deviceMap[key].test(userAgent)) || 'Unknown Device';
    const browser = Object.keys(browserMap).find((key) => browserMap[key].test(userAgent)) || 'Unknown Browser';

    return `${browser} - ${device}`;
}
