import Swal from 'sweetalert2';

function showSwalMessage(title, message, reload = false, redirectUrl = null) {
    const isSuccess = title.toLowerCase() === 'success';
    Swal.fire({
        title: title,
        text: message,
        icon: title.toLowerCase(),
        buttonsStyling: false,
        confirmButtonText: "Ok",
        showConfirmButton: !isSuccess,
        timer: isSuccess ? 1500 : undefined,
        timerProgressBar: isSuccess,
        customClass: {
            confirmButton: "btn btn-primary waves-effect waves-light",
            icon: 'small-icon'
        }
    }).then(function () {

        if (redirectUrl) {
            window.location.href = redirectUrl;
            return;
        }

        if (reload) {
            location.reload();
        }

    });
}

function confirmAction(title, text, callback, param = null) {
    Swal.fire({
    title: title,
    text: text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'Cancel'
    }).then((result) => {
    if (result.isConfirmed) {
        callback(param);
    }
    });
}
export { showSwalMessage, confirmAction };