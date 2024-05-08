$(document).on('click', '.delete-btn', function() {
    let confirmation = confirm('Voulez-vous vraiment supprimer cette notification ?');
    if (!confirmation) {
        return;
    }
    let idNotification = $(this).closest('.card').data('notification-id');
    let divNotification = $(this).closest('.card');
    $.ajax({
        type: 'POST',
        url: 'php_tool/notificationManager.php',
        data: {
            idNotification: idNotification 
        },
        success: function(response) {
            divNotification.remove();
        }
    });
});

$(document).ready(function() {
    let formData = new FormData();
    $.ajax({
        type: 'POST',
        url: 'php_tool/notificationManager.php',
        data: formData,
        processData: false,
        contentType: false,
    });
});