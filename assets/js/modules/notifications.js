export function showNotification(title, message, type, timeOut = 2000) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toastr-top-right',
        timeOut,
    };

    toastr[type](message, title);
}

export function showErrorDialog(error){
    const errorDialogElement = document.getElementById('error-dialog');

    if (errorDialogElement) {
        errorDialogElement.innerHTML = error;
        $('#system-error-modal').modal('show');
    }
    else {
        console.error('Error dialog element not found.');
    }    
}

export function setNotification(title, message, type) {
    sessionStorage.setItem('notificationTitle', title);
    sessionStorage.setItem('notificationMessage', message);
    sessionStorage.setItem('notificationType', type);
}

export function checkNotification() {
    const notificationTitle = sessionStorage.getItem('notificationTitle');
    const notificationMessage = sessionStorage.getItem('notificationMessage');
    const notificationType = sessionStorage.getItem('notificationType');

    if (notificationTitle && notificationMessage && notificationType) {
        sessionStorage.clear();
        showNotification(notificationTitle, notificationMessage, notificationType);
    }
}