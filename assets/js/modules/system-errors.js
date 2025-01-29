import { showErrorDialog } from './notifications.js';

export function handleSystemError(error) {
    const errorMessage = `Error: ${error}`;
    showErrorDialog(errorMessage);
}