$(document).ready(function () {
     /* Modifier le profil */
     $('#formProfile').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: 'php_tool/change_profile.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.error) {
                    $('#error-message-r').text(response.message);
                } else {
                    $('#modalProfile').modal('hide');
                    var profileToast = new bootstrap.Toast(document.getElementById('changeProfileToast'));
                    profileToast.show();
                    setTimeout(function () {
                        if (response.changedpseudo) {
                            window.location.href = "profile.php?pseudo="+response.pseudo;
                        } else {
                            location.reload(true);
                        }
                    }, 2000);
                }
                document.getElementById('formProfile').reset();
            }
        });
    });
});