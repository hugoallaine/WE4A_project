var buttons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#modalPost"]');

buttons.forEach(function (button) {
    button.addEventListener('click', function () {
        var tweetId = button.getAttribute('data-tweet-id');
        var input = document.querySelector('input[name="id_parent"]');
        input.value = tweetId;
    });
});

function TogglePassword(checked) {
    var x = document.getElementById("password");
    if (checked) {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

function TogglePasswordRegister(checked) {
    var x = document.getElementById("password-r");
    var y = document.getElementById("password-r-repeat");
    if (checked) {
        x.type = "text";
        y.type = "text";
    } else {
        x.type = "password";
        y.type = "password";
    }
}

$(document).ready(function () {
    $('.textAreaPost').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            $('#formPostId').submit();
        }
    });

    /* Envoi du formulaire d'ajout de post */
    $('#formPostId').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php_tool/submit_post.php',
            data: formData,
            success: function (response) {
                document.getElementById('formPostId').reset();
                $('#modalPost').modal('hide');
            }
        });
    });

    /* Login */
    $('#formLoginId').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
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
                    var loginToast = new bootstrap.Toast(document.getElementById('loginToast'));
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
        var formData = new FormData(this);
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
                    var registerToast = new bootstrap.Toast(document.getElementById('registerToast'));
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

    /* Like post */
    $('.like-button').click(function () {
        var postId = $(this).data('post-id');
        var likeImage = $(this).find('img');
        $.ajax({
            url: 'php_tool/like_post.php',
            type: 'POST',
            data: {
                post_id: postId
            },
            success: function (response) {
                if (response === 'liked') {
                    likeImage.attr('src', '/WE4A_project/img/icon/liked.png');
                } else {
                    likeImage.attr('src', '/WE4A_project/img/icon/like.png');
                }
            }
        });
    });
});