let formData = new FormData();
$.ajax({
    type: 'POST',
    url: 'php_tool/notificationManager.php',
    data: formData,
    processData: false,
    contentType: false,
});