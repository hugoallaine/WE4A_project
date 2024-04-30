function insertPost(post, element) {

    let pictureHtml = "";
    
    if (post.picture) {
        pictureHtml = `<a href='${post.picture}'><img src='${post.picture}' class='rounded' width='400' height='320' style='object-fit: cover;'></a>`;
    }

    var html = `
        <div class='card rounded-0'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-2 col-3 text-center'>
                        <a class='link-secondary link-underline link-underline-opacity-0' href='/WE4A_project/profile.php?pseudo=${post.pseudo}'>
                        <img src='${post.avatar}' width='64' height='64' alt='Avatar' class='rounded-circle mr-2' style='object-fit: cover;'>
                        <h5 class='card-title m-0'>${post.pseudo}</h5>
                        </a>
                        <p class='card-subtitle text-muted'>${post.date}</p>
                    </div>
                    <div class='col p-0 post' data-post-id='${post.id}'>
                        <p>${post.content}</p>
                        ${pictureHtml}    
                    </div>
                    <div class='col-1'>
                        <div class='row'>
                            <div class='col-12 p-0'>
                                <button class='btn like-button' ${isConnected ? `data-post-id='${post.id}'` : `data-bs-toggle='modal' data-bs-target='#modalLogin'`}>
                                    <img src='${post.like_image}' alt='like button' class='img-fluid' >
                                </button>
                            </div>
                            <div class='col-12 p-0 mb-2 text-center'>
                                <strong>${post.like_count}</strong>
                            </div>
                            <div class='col-12 p-0'>
                                <button class='btn' type='button' ${isConnected ? `data-bs-toggle='modal' data-bs-target='#modalPost' data-tweet-id='${post.id}'` : `data-bs-toggle='modal' data-bs-target='#modalLogin'`}>
                                    <img src='/WE4A_project/img/icon/response.png' alt='response button' class='img-fluid'>
                                </button>
                            </div>
                            <div class='col-12 p-0 mb-2 text-center'>
                                <strong>${post.comment_count}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    element.innerHTML = html + element.innerHTML;
}

function ListRandomPosts(token) {
    start = $('#posts-container .post').length;
    $.ajax({
        url: "php_tool/postManager.php",
        type: 'GET',
        data: {
            echoListRandomPosts: true,
            start: start,
            token: sessionStorage.getItem('token')
        },
        success: function (response) {
            var responses = JSON.parse(response);
            for (rep of responses) {
                var element = document.querySelector('#posts-container');
                insertPost(rep, element);
            }
        }
    });
}

$(document).on('click', '.post, .like-button, [data-bs-toggle="modal"][data-bs-target="#modalPost"]', function () {
    var postId = $(this).data('post-id');

    if ($(this).hasClass('post')) {
        /* Open modal with responses */
        $('#modalResponses .modal-body').empty();

        $.ajax({
            url: "php_tool/postManager.php",
            type: 'GET',
            data: {
                echoResponses: true,
                postId: postId
            },
            success: function (response) {
                var responses = JSON.parse(response);
                if (responses.length === 0) {
                    var element = document.querySelector('#modalResponses .modal-body');
                    element.innerHTML = "<h5 class='text-center'>Pas encore de réponse, soyez le premier à répondre !</h5>";
                }
                else {
                    for (rep of responses) {
                        var element = document.querySelector('#modalResponses .modal-body');
                        insertPost(rep, element);
                    }
                }
            }
        });

        $('#modalResponses').modal('show');
    } else if ($(this).hasClass('like-button')) {
        /* Like button */
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
    } else if ($(this).is('[data-bs-toggle="modal"][data-bs-target="#modalPost"]')) {
        /* Button that triggers the modal */
        var tweetId = $(this).data('tweet-id');
        var input = $('input[name="id_parent"]');
        input.val(tweetId);
    }
});

$(document).ready(function () {

    let token;

    if (sessionStorage.getItem('token')) {
        token = sessionStorage.getItem('token');
    } else {
        token = Math.floor(Math.random() * 100000);
        sessionStorage.setItem('token', token);
    }

    /* Check if user is connected */
    $.ajax({
        url: "php_tool/checkSession.php",
        type: 'GET',
        success: function (response) {
            if (response.status === true) {
                isConnected = true;
            } else {
                isConnected = false;
            }
            /* Load posts */
            ListRandomPosts(token);
        }
    });

    /* Load more posts */

    $('#posts-container').on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
            ListRandomPosts(token);
        }
    });
});