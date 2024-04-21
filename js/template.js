function TogglePassword(checked) {
    let x = document.getElementById("password");
    if (checked) {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

function TogglePasswordRegister(checked) {
    let x = document.getElementById("password-r");
    let y = document.getElementById("password-r-repeat");
    if (checked) {
        x.type = "text";
        y.type = "text";
    } else {
        x.type = "password";
        y.type = "password";
    }
}

function checkNotificationsNumber() {
    let nbNotif = document.getElementById("nbNotif");
    $.ajax({
        type: 'GET',
        url: 'php_tool/notificationManager.php?count=true',
        success: function (response) {
            if (response.error) {
                console.log("Récupération des notifications impossible");
            } else {
                if (response.count > 0) {
                    nbNotif.textContent = response.count;
                    nbNotif.style.display = "inline";
                } else {
                    nbNotif.textContent = '';
                    nbNotif.style.display = "none";
                }
            }
        }
    });
}

$(document).ready(function () {
    /* Notifications */
    checkNotificationsNumber();
    setInterval(checkNotificationsNumber, 10000);

    /* Login */
    $('#formLoginId').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php_tool/login.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message').text(response.message);
                    document.getElementById('formLoginId').reset();
                } else if (response.tfa) {
                    $("#tfaDiv").css("display", "block");
                    $('#error-message').text("Veuillez entrer le code de double authentification");
                } else {
                    $('#modalLogin').modal('hide');
                    let loginToast = new bootstrap.Toast(document.getElementById('loginToast'));
                    loginToast.show();
                    document.getElementById('formLoginId').reset();
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
            }
        });
    });

    /* Register */
    $('#formRegisterId').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: 'php_tool/register.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.error) {
                    $('#error-message-r').text(response.message);
                } else {
                    $('#modalRegister').modal('hide');
                    let registerToast = new bootstrap.Toast(document.getElementById('registerToast'));
                    registerToast.show();
                }
                document.getElementById('formRegisterId').reset();
            }
        });
    });

    /* Logout */
    $('#logout-button').click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'php_tool/logout.php',
            success: function () {
                location.reload();
            },
        });
    });
});