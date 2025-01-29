import { disableButton, enableButton } from '../utils/form-utilities.js';
import { showNotification, setNotification } from '../modules/notifications.js';
import { handleSystemError } from '../modules/system-errors.js';

/**
 * Handles the form submission for login authentication.
 * 
 * @param {Event} e - The form submit event.
 */
const handleLoginFormSubmit = async (e) => {
    e.preventDefault(); // Prevent the default form submission

    const submitButtonId = 'signin';
    disableButton(submitButtonId); // Disable button while submitting

    const formData = new FormData(e.target); // Collect form data
    const baseUrl = window.location.origin; // Get base URL of the app

    try {
        const response = await authenticateUser(baseUrl, formData); // Authenticate user
        const data = await response.json(); // Parse JSON response

        // Handle the response based on conditions
        if (response.ok) {
            handleSuccessResponse(data);
        } else {
            handleSystemError(data.error);
        }
    } catch (error) {
        handleSystemError(error); // Handle system error
    } finally {
        enableButton(submitButtonId); // Re-enable button after request
    }
};

/**
 * Makes a POST request to the authentication endpoint.
 *
 * @param {string} baseUrl - The base URL of the application.
 * @param {FormData} formData - The form data to be sent in the request.
 * @returns {Promise<Response>} - The response from the fetch request.
 */
const authenticateUser = (baseUrl, formData) =>
    fetch(`${baseUrl}/authenticate`, {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' },
    });

/**
 * Handles the success response from the authentication API.
 * 
 * @param {Object} data - The data returned from the API.
 */
const handleSuccessResponse = ({ success, passwordExpired, title, message, messageType, redirectLink }) => {
    if (success) {
        window.location.href = redirectLink; // Redirect on successful login
    } else if (passwordExpired) {
        setNotification(title, message, messageType);
        window.location.href = redirectLink; // Redirect to password change page
    } else {
        showNotification(title, message, messageType); // Show general notification
    }
};

// Add event listener to the form for submission
document.querySelector('#signin-form').addEventListener('submit', handleLoginFormSubmit);