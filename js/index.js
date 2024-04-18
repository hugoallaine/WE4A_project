var buttons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#modalPost"]');

buttons.forEach(function (button) {
    button.addEventListener('click', function () {
        var tweetId = button.getAttribute('data-tweet-id');
        var input = document.querySelector('input[name="id_parent"]');
        input.value = tweetId;
    });
});

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
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: 'php_tool/submit_post.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                document.getElementById('formPostId').reset();
                $('#modalPost').modal('hide');
            }
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
