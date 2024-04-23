var buttons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#modalPost"]');

buttons.forEach(function (button) {
    button.addEventListener('click', function () {
        var tweetId = button.getAttribute('data-tweet-id');
        var input = document.querySelector('input[name="id_parent"]');
        input.value = tweetId;
    });
});

$(document).ready(function () {
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
