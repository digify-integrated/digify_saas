import { showErrorDialog } from './notifications.js';

/**
 * Handles system errors and displays an error dialog.
 *
 * @param {Error | string | any} error - The error to handle. Can be an Error object, string, or unknown type.
 */
export const handleSystemError = (error) => {
    const errorMessage =
        error instanceof Error 
            ? `Error: ${error.message}` 
            : typeof error === "string" 
                ? `Error: ${error}` 
                : "An unexpected error occurred.";

    console.error(error instanceof Error ? error.stack : "Unhandled error type:", error);

    showErrorDialog(errorMessage);
};
