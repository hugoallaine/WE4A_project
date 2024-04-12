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

$(document).ready(function(){
    $('.textAreaPost').on('keydown', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $('#formPostId').submit();
        }
    });

    /* Envoi du formulaire d'ajout de post */
    $('#formPostId').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php_tool/submit_post.php',
            data: formData,
            success: function(response){
                document.getElementById('formPostId').reset();
                $('#modalPost').modal('hide');
            }
        });
    });

    /* Login */
    $('#formLoginId').submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php_tool/main_login.php',
            data: formData,
            success: function(response){
                if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    $('#modalLogin').modal('hide');
                    var loginToast = new bootstrap.Toast(document.getElementById('loginToast'));
                    loginToast.show();
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
                document.getElementById('formLoginId').reset();
            },
        });
    });

    /* Register */
    $('#formRegisterId').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('avatar-r', $('#avatar')[0].files[0]);
        $.ajax({
            type: 'POST',
            url: 'php_tool/register.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                if (response.error) {
                    $('#error-message-r').text(response.message);
                } else {
                    $('#modalRegister').modal('hide');
                    var registerToast = new bootstrap.Toast(document.getElementById('registerToast'));
                    registerToast.show();
                }
                //document.getElementById('formRegisterId').reset();
            },
        });
    });

    /* Logout */
    $('#logout-button').click(function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'php_tool/logout.php',
            success: function(){
                location.reload();
            },
            error: function(){
                console.log('Erreur lors de la déconnexion');
            }
        });
    });

    /* Like post */
    $('.like-button').click(function() {
        var postId = $(this).data('post-id');
        var likeImage = $(this).find('img');
        $.ajax({
            url: 'php_tool/like_post.php',
            type: 'POST',
            data: {
                post_id: postId
            },
            success: function(response) {
                if (response === 'liked') {
                    likeImage.attr('src', '/WE4A_project/img/icon/liked.png');
                } else {
                    likeImage.attr('src', '/WE4A_project/img/icon/like.png');
                }
            }
        });
    });
});