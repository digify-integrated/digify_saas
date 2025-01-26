import { disableButton, enableButton } from '../utils/form-utilities.js';
import { showNotification, setNotification } from '../modules/notifications.js';

document.querySelector('#signin-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    disableButton('signin');
    
    const formData = new FormData(e.target);
    const baseUrl = window.location;
    /*const response = await fetch(`${baseUrl}/authenticate`, {
        method: 'POST',
        body: formData,
    });

    if (response.ok) {
        window.location.href = '/dashboard';
    } else {
        showNotification('Invalid', response.ok, 'error');
    }

    enableButton('signin');*/
});