$(document).ready(function () {
    /* Modifier le profil */
    $('#formProfile').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: 'php_tool/changeAccount.php',
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
                            window.location.href = "profile.php?pseudo=" + response.pseudo;
                        } else {
                            location.reload(true);
                        }
                    }, 2000);
                }
                document.getElementById('formProfile').reset();
            }
        });
    });

    /* Suivre un utilisateur */
    $('#formFollow').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php_tool/follow.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    if (response.message == 'followed') {
                        $('#btnFollow').text("Ne plus suivre");
                        $('#btnFollow').removeClass("btn-primary");
                        $('#btnFollow').addClass("btn-secondary");
                    } else {
                        $('#btnFollow').text("Suivre");
                        $('#btnFollow').removeClass("btn-secondary");
                        $('#btnFollow').addClass("btn-primary");
                    }
                }
            }
        });
    });
});