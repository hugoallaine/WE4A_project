/**
 * function to show the delete notification modal
 */
$(document).on({
    'click': function () {
        $('#deleteNotificationModal').modal('show');
        let idNotification = $(this).closest('.card').data('notification-id');
        let divNotification = $(this).closest('.card');
        $('#deleteNotificationBtn').data('idNotification', idNotification);
        $('#deleteNotificationBtn').data('divNotification', divNotification);
    }
}, '.delete-btn').on({
    'click': function () {
        $('#deleteNotificationModal').modal('hide');
        let idNotification = $(this).data('idNotification');
        let divNotification = $(this).data('divNotification');
        $.ajax({
            type: 'POST',
            url: 'php/notificationManager.php',
            data: {
                idNotification: idNotification
            },
            success: function (response) {
                divNotification.remove();
            }
        });
    }
}, '#deleteNotificationBtn');

/**
 * function to read all notifications
 */
$(document).ready(function () {
    let formData = new FormData();
    $.ajax({
        type: 'POST',
        url: 'php/notificationManager.php',
        data: formData,
        processData: false,
        contentType: false,
    });
});