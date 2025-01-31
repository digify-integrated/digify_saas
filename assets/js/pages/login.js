import { disableButton, enableButton, initPasswordToggle } from '../utils/form-utilities.js';
import { showNotification, setNotification } from '../modules/notifications.js';
import { handleSystemError } from '../modules/system-errors.js';
import { getDeviceInfo } from '../utils/device-info.js';
import { displayValidationErrors, setupDynamicErrorRemoval } from '../utils/form-validation.js';

document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
});

// Custom error messages
const customMessages = {
    username: {
        required: 'Please enter your username'
    },
    password: {
        required: "Please enter your password"
    },
};

/**
 * Validate the login form inputs.
 * 
 * @param {FormData} formData - The form data to be validated.
 * @returns {Object} - An object containing validation result and error messages if any.
 */
const validateLoginForm = (formData) => {
    const errors = {};

    const username = formData.get('username').trim();
    const password = formData.get('password').trim();

    if (!username) {
        errors.username = customMessages.username.required;
    }

    if (!password) {
        errors.password = customMessages.password.required;
    }

    return errors;
};

/**
 * Handles the form submission for login authentication.
 * 
 * @param {Event} e - The form submit event.
 */
const handleLoginFormSubmit = async (e) => {
    e.preventDefault(); // Prevent the default form submission

    const submitButtonId = 'signin';
    disableButton(submitButtonId);

    const formData = new FormData(e.target);
    
    const validationErrors = validateLoginForm(formData);

    if (Object.keys(validationErrors).length > 0) {
        displayValidationErrors(validationErrors);
        enableButton(submitButtonId);
        return;
    }
    
    formData.append('device_info', getDeviceInfo());

    const baseUrl = window.location.origin;

    try {
        const response = await authenticateUser(baseUrl, formData);
        const data = await response.json();

        if (response.ok) {
            handleSuccessResponse(data);
        } else {
            handleSystemError(data.error);
        }
    } catch (error) {
        handleSystemError(error);
    } finally {
        enableButton(submitButtonId);
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
        //window.location.href = redirectLink;
        showNotification(title, message, messageType);
    } else if (passwordExpired) {
        setNotification(title, message, messageType);
        window.location.href = redirectLink;
    } else {
        showNotification(title, message, messageType);
    }
};

// Add event listener to the form for submission
document.querySelector('#signin-form').addEventListener('submit', handleLoginFormSubmit);

// Set up dynamic error removal
setupDynamicErrorRemoval('#signin-form');