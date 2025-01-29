/**
 * Displays a notification using the Toastr library.
 *
 * @param {string} title - The title of the notification.
 * @param {string} message - The message content of the notification.
 * @param {string} [type='warning'] - The type of notification (e.g., success, error, info, warning).
 * @param {number} [timeOut=2000] - Duration in milliseconds before the notification disappears.
 */
export const showNotification = (title = '', message = '', type = 'warning', timeOut = 2000) => {
    if (window.toastr?.[type]) {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toastr-top-right',
            timeOut,
        };

        toastr[type](message, title);
    } else {
        console.error(`Toastr is not loaded or invalid notification type: ${type}`);
    }
};

/**
 * Displays an error message inside a modal dialog.
 *
 * @param {string} error - The error message to be displayed.
 */
export const showErrorDialog = (error) => {
    const errorDialog = document.getElementById('error-dialog');
    const modal = document.getElementById('system-error-modal');

    if (!errorDialog) return console.error('Error dialog element not found.');
    if (!modal) return console.error('System error modal not found.');

    errorDialog.textContent = error;
    $(modal).modal('show');
};

/**
 * Stores a notification in sessionStorage for later retrieval.
 *
 * @param {string} title - The title of the notification.
 * @param {string} message - The message content of the notification.
 * @param {string} [type='info'] - The type of notification (e.g., success, error, info, warning).
 */
export const setNotification = (title, message, type = 'info') => {
    sessionStorage.setItem('notificationTitle', title);
    sessionStorage.setItem('notificationMessage', message);
    sessionStorage.setItem('notificationType', type);
};

/**
 * Checks for stored notifications in sessionStorage and displays them if available.
 */
export const checkNotification = () => {
    const title = sessionStorage.getItem('notificationTitle');
    const message = sessionStorage.getItem('notificationMessage');
    const type = sessionStorage.getItem('notificationType');

    if (title && message && type) {
        sessionStorage.removeItem('notificationTitle');
        sessionStorage.removeItem('notificationMessage');
        sessionStorage.removeItem('notificationType');
        showNotification(title, message, type);
    }
};
