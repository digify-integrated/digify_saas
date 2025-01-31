/**
 * Places the error message in the correct position.
 * 
 * @param {HTMLElement} element - The input field with validation error.
 * @param {string} errorMessage - The error message to display.
 */
export const placeError = (element, errorMessage) => {
    removeError(element); // Remove previous error if exists

    const error = document.createElement('div');
    error.classList.add('error', 'text-danger');
    error.textContent = errorMessage;

    const inputGroup = element.closest('.input-group');

    if (inputGroup) {
        inputGroup.insertAdjacentElement('afterend', error);
    } else if (element.classList.contains('select2-hidden-accessible')) {
        const select2Element = element.nextElementSibling?.querySelector('.select2-selection');
        if (select2Element) {
            select2Element.insertAdjacentElement('afterend', error);
        }
    } else {
        element.insertAdjacentElement('afterend', error);
    }

    highlightField(element);
};

/**
 * Highlights the invalid field.
 * 
 * @param {HTMLElement} element - The input field to highlight.
 */
export const highlightField = (element) => {
    let target = element;
    if (element.classList.contains('select2-hidden-accessible')) {
        target = element.nextElementSibling?.querySelector('.select2-selection');
    }
    target?.classList.add('is-invalid');
};

/**
 * Removes the error message and unhighlights the field.
 * 
 * @param {HTMLElement} element - The input field to remove error styling from.
 */
export const removeError = (element) => {
    let target = element;
    if (element.classList.contains('select2-hidden-accessible')) {
        target = element.nextElementSibling?.querySelector('.select2-selection');
    }

    target?.classList.remove('is-invalid');

    // Ensure the correct error message is targeted
    const error = element.closest('.input-group')?.nextElementSibling;
    if (error?.classList.contains('error')) {
        error.remove();
    } else {
        element.parentElement?.querySelector('.error')?.remove();
    }
};

/**
 * Displays validation error messages for multiple fields.
 * 
 * @param {Object} errors - The validation errors to display.
 */
export const displayValidationErrors = (errors) => {
    for (const [fieldName, errorMessage] of Object.entries(errors)) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            placeError(field, errorMessage);
        }
    }
};

/**
 * Removes error messages dynamically as the user types.
 * 
 * @param {string} formSelector - The selector for the form.
 */
export const setupDynamicErrorRemoval = (formSelector) => {
    document.querySelectorAll(`${formSelector} input, ${formSelector} select, ${formSelector} textarea`).forEach((input) => {
        input.addEventListener('input', () => removeError(input));
        input.addEventListener('change', () => removeError(input));
        input.addEventListener('blur', () => removeError(input)); // Fix for password & auto-fill fields
    });
};
