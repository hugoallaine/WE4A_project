$(document).ready(function () {
    /* Modifier les informations du compte */
    $('#formChangeInfo').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php/changeAccount.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message-info').text(response.message);
                } else {
                    $('#error-message-info').text('');
                    let changeInfoToast = new bootstrap.Toast(document.getElementById('changeInfoToast'));
                    changeInfoToast.show();
                }
            }
        });
    });

    /* Modifier le mot de passe */
    $('#formChangePassword').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php/changeAccount.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message-password').text(response.message);
                } else {
                    let changePasswordToast = new bootstrap.Toast(document.getElementById('changePasswordToast'));
                    changePasswordToast.show();
                }
                document.getElementById('formChangePassword').reset();
            }
        });
    });

    /* Activer la 2FA */
    $('#formEnableTfa').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php/changeAccount.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    let activationTfaToast = new bootstrap.Toast(document.getElementById('activationTfaToast'));
                    activationTfaToast.show();
                    setTimeout(function () {
                        location.reload(true);
                    }, 2000);
                }
                document.getElementById('formEnableTfa').reset();
            }
        });
    });

    /* Désactiver la 2FA */
    $('#formDisableTfa').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php/changeAccount.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    let desactivationTfaToast = new bootstrap.Toast(document.getElementById('desactivationTfaToast'));
                    desactivationTfaToast.show();
                    setTimeout(function () {
                        location.reload(true);
                    }, 2000);
                }
                document.getElementById('formDisableTfa').reset();
            }
        });
    });

    /* Supprimer le compte */
    $('#formDeleteAccount').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php/changeAccount.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    let deleteAccountToast = new bootstrap.Toast(document.getElementById('deleteAccountToast'));
                    deleteAccountToast.show();
                    setTimeout(function () {
                        location.href = 'index.php';
                    }, 2000);
                }
                document.getElementById('formDeleteAccount').reset();
            }
        });
    });
});