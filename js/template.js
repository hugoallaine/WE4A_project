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

function checkNotificationsNumberUnread() {
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

function displayResults(query) {
    $('#search-results').empty();
    if (query.users.length > 0) {
        $('#search-results').append('<h6 class="text-center">Utilisateurs</h6>');
        $.each(query.users, function(index, user) {
            var resultElement = $('<div>').text('ID: ' + user.id + ', Pseudo: ' + user.pseudo);
            $('#search-results').append(resultElement);
        });
        $('#search-results').append('<hr>');
    }
    if (query.posts.length > 0) {
        $('#search-results').append('<h6 class="text-center">Posts</h6>');
        $.each(query.posts, function(index, post) {
            var resultElement = $('<div>').text('ID: ' + post.id + ', Content: ' + post.content);
            $('#search-results').append(resultElement);
        });
    }
    $('#search-results').show();
}

$('#search-bar').on('input', function() {
    let searchTerm = $(this).val();
    if (searchTerm.length >= 3) {
        $.ajax({
            type: 'GET',
            url: 'php_tool/search.php',
            data: { 
                q: searchTerm 
            },
            success: function(response) {
                if (response.error) {
                    console.log(response.message);
                } else {
                    displayResults(response.query);
                }
            }
        });
    } else {
        $('#search-results').empty();
    }
});

$(document).on('click', function(e) {
    if (!$(e.target).closest('.search-container').length) {
        $('#search-results').hide();
    }
});

$(document).ready(function () {
    /* Envoi du formulaire d'ajout de post */
    $('#formPostId').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let text = formData.get('textAreaPostId');
        if (text.length > 290) {
            let errorDiv = $('.modal-body').find('.alert-danger');
            if (errorDiv.length === 0) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-2 mb-0';
                errorDiv.textContent = 'Le texte ne peut pas dépasser 290 caractères.';
                $('.modal-body').append(errorDiv);
            }
            return;
        }
        $.ajax({
            type: 'POST',
            url: 'php_tool/postManager.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                response = JSON.parse(response);
                document.getElementById('formPostId').reset();
                $('#modalPost').modal('hide');
                if (document.title.includes("Accueil")) {
                    insertPost(response, document.querySelector('#posts-container'));
                }
            }
        });
    });
    
    /* Notifications */
    checkNotificationsNumberUnread();
    setInterval(checkNotificationsNumberUnread, 10000);

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
                    if (response.info) {
                        console.log("Info: " + response.message);
                        let avatarToast = new bootstrap.Toast(document.getElementById('avatarToast'));
                        avatarToast.show();
                    }
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