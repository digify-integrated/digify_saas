/**
 * Disables a button and adds a loading spinner next to its text.
 *
 * @param {string} buttonId - The ID of the button to disable.
 */
export const disableButton = (buttonId) => {
    const button = document.getElementById(buttonId);
    if (!button) return console.warn(`disableButton: Button with ID "${buttonId}" not found.`);

    // Store the original button text if not already stored
    button.dataset.originalText ??= button.innerHTML.trim();

    // Disable the button and append a spinner
    button.disabled = true;
    button.innerHTML = `
        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        ${button.dataset.originalText}
    `;
};

/**
 * Enables a button and restores its original text.
 *
 * @param {string} buttonId - The ID of the button to enable.
 */
export const enableButton = (buttonId) => {
    const button = document.getElementById(buttonId);
    if (!button) return console.warn(`enableButton: Button with ID "${buttonId}" not found.`);

    // Enable the button and restore original text
    button.disabled = false;
    if (button.dataset.originalText) {
        button.innerHTML = button.dataset.originalText;
        delete button.dataset.originalText;
    }
};

/**
 * Initializes password visibility toggle for all elements with the class `password-addon`.
 *
 */
export function initPasswordToggle() {
    document.addEventListener('click', (event) => {
        const toggleButton = event.target.closest('.password-addon');
        if (!toggleButton) return;

        const inputField = toggleButton.previousElementSibling;
        const eyeIcon = toggleButton.querySelector('i');

        if (inputField && eyeIcon) {
            togglePasswordVisibility(inputField, eyeIcon, toggleButton);
        }
    });
}

/**
 * Toggles the password field visibility.
 * 
 * @param {HTMLInputElement} inputField - The password input field.
 * @param {HTMLElement} eyeIcon - The icon inside the toggle button.
 * @param {HTMLButtonElement} toggleButton - The button that toggles password visibility.
 */
const togglePasswordVisibility = (inputField, eyeIcon, toggleButton) => {
    const isPassword = inputField.type === 'password';
    inputField.type = isPassword ? 'text' : 'password';

    // Toggle eye icon classes
    eyeIcon.classList.toggle('ki-eye-slash', !isPassword);
    eyeIcon.classList.toggle('ki-eye', isPassword);

    // Update the aria-label for accessibility
    toggleButton.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');

    // Use requestAnimationFrame to prevent layout thrashing
    requestAnimationFrame(() => inputField.focus());
};

