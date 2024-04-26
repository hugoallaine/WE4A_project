function changeText() {
    let btn = document.getElementById("btnFollow");
    if (btn.textContent == "Suivi") {
        btn.textContent = "Ne plus suivre";
        btn.classList.remove("btn-primary");
        btn.classList.add("btn-secondary");
    }
}

function resetText() {
    let btn = document.getElementById("btnFollow");
    if (btn.textContent == "Ne plus suivre") {
        btn.textContent = "Suivi";
        btn.classList.remove("btn-secondary");
        btn.classList.add("btn-primary");
    }
}

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
                    $('#error-message').text(response.message);
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
        if ($('#btnFollow').text() == "Ne plus suivre") {
            var confirmation = confirm("Êtes-vous sûr de vouloir ne plus suivre cet utilisateur ?");
            if (!confirmation) {
                return;
            }
        }
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
                        $('#btnFollow').text("Suivi");
                        $('#nbFollowers').text(parseInt($('#nbFollowers').text()) + 1);
                    } else {
                        $('#btnFollow').text("Suivre");
                        $('#nbFollowers').text(parseInt($('#nbFollowers').text()) - 1);
                    }
                }
            }
        });
    });
});