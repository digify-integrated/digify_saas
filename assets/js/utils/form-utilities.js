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