export function disableButton(buttonId) {
    const button = document.getElementById(buttonId);
    if (button) {

        if (!button.dataset.originalText) {
            button.dataset.originalText = button.innerHTML;
        }
       
        button.disabled = true;
        button.innerHTML = `
            <span>
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>`;
    }
}

export function enableButton(buttonId) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.disabled = false;
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
            delete button.dataset.originalText;
        }
    }
}