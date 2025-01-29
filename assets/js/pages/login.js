import { disableButton, enableButton } from '../utils/form-utilities.js';
import { showNotification, setNotification } from '../modules/notifications.js';
import { handleSystemError } from '../modules/system-errors.js';

/**
 * Handles the form submission for login authentication.
 * @param {Event} e - The form submit event.
 */
const handleLoginFormSubmit = async (e) => {
    e.preventDefault(); // Prevent the default form submission

    const submitButtonId = 'signin';
    disableButton(submitButtonId);

    const formData = new FormData(e.target); // Collect form data
    const baseUrl = `${window.location.origin}`;

    try {
        const response = await authenticateUser(baseUrl, formData);
        const data = await response.json(); // Parse JSON response

        // Handle response based on success/failure and other conditions
        if (response.ok) {
            handleSuccessResponse(data);
        }
        else {
            handleSystemError(data.error);
        }
    } catch (error) {
        handleSystemError(error);
    } finally {
        enableButton(submitButtonId); // Re-enable button after the request
    }
};

/**
 * Makes a POST request to the authentication endpoint.
 * @param {string} baseUrl - The base URL of the application.
 * @param {FormData} formData - The form data to be sent in the request.
 * @returns {Promise<Response>} - The response from the fetch request.
 */
const authenticateUser = async (baseUrl, formData) => {
    return fetch(`${baseUrl}/authenticate`, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
        },
    });
};

/**
 * Handles the success response from the authentication API.
 * @param {Object} data - The data returned from the API.
 */
const handleSuccessResponse = (data) => {
    if (data.success) {
        window.location.href = data.redirectLink; // Redirect on successful login
    } else if (data.passwordExpired) {
        setNotification(data.title, data.message, data.messageType);
        window.location.href = data.redirectLink; // Redirect to password change page
    } else {
        showNotification(data.title, data.message, data.messageType); // Show general notification
    }
};

// Add event listener to the form for submission
document.querySelector('#signin-form').addEventListener('submit', handleLoginFormSubmit);