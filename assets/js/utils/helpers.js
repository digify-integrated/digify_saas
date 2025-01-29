import { showNotification } from '../modules/notifications.js';

/**
 * Copies text content from a specified element to the clipboard.
 *
 * @param {string} elementID - The ID of the element containing the text to copy.
 */
export const copyToClipboard = (elementID) => {
    const textElement = document.getElementById(elementID);
    
    if (!textElement) return showNotification('Copy Error', 'Element not found', 'error');

    const text = textElement.textContent.trim();
    if (!text) return showNotification('Copy Error', 'No text to copy', 'error');

    navigator.clipboard.writeText(text)
        .then(() => showNotification('Success', 'Copied to clipboard', 'success'))
        .catch(() => showNotification('Error', 'Failed to copy', 'error'));
};

/**
 * Resets a form to its default state.
 *
 * @param {string} formId - The ID of the form to reset.
 */
export const resetForm = (formId) => {
    const form = document.getElementById(formId);
    if (!form) return console.warn(`resetForm: Form with ID "${formId}" not found.`);

    // Reset Select2 dropdowns if they exist
    $(form).find('.select2').val('').trigger('change');

    // Remove validation error states
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Reset form fields
    form.reset();
};

/**
 * Detects and returns the user's device and browser information.
 *
 * @returns {string} A formatted string with the browser and device information.
 */
export const getDeviceInfo = () => {
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
        'Internet Explorer': /MSIE|Trident/i
    };

    const device = Object.entries(deviceMap).find(([, regex]) => regex.test(userAgent))?.[0] || 'Unknown Device';
    const browser = Object.entries(browserMap).find(([, regex]) => regex.test(userAgent))?.[0] || 'Unknown Browser';

    return `${browser} - ${device}`;
};