function insertPost(post, element, isOrginalPost = false, insertAfter = false) {

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
                    <div class='col p-0 post' style='cursor: pointer;' data-post-id='${post.id}' data-post-id-parent='${post.id_parent}' ${isOrginalPost ? "data-is-original-post='true'" : ""}>
                        <p>${post.content}</p>
                        ${pictureHtml}    
                    </div>
                    <div class='col-1'>
                        <div class='row'>
                            <div class='col-12 p-0'>
                                <button class='btn like-button' ${isConnected ? `data-post-id='${post.id}'` : `data-bs-toggle='modal' data-bs-target='#modalLogin'`}>
                                    <img data-like-image-for-post='${post.id}' src='${post.like_image}' alt='like button' class='img-fluid' >
                                </button>
                            </div>
                            <div class='col-12 p-0 mb-2 text-center'>
                                <strong data-like-count-for-post='${post.id}'>${post.like_count}</strong>
                            </div>
                            <div class='col-12 p-0'>
                                <button class='btn' type='button' ${isConnected ? `data-bs-toggle='modal' data-bs-target='#modalPost' data-tweet-id='${post.id}'` : `data-bs-toggle='modal' data-bs-target='#modalLogin'`}>
                                    <img src='/WE4A_project/img/icon/response.png' alt='response button' class='img-fluid'>
                                </button>
                            </div>
                            <div class='col-12 p-0 mb-2 text-center'>
                                <strong data-response-count-for-post='${post.id}'>${post.comment_count}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    if (insertAfter) {
        element.innerHTML = element.innerHTML + html;
    }
    else {
        element.innerHTML = html + element.innerHTML;
    }
}

function ListLatestPosts() {
    start = $('#posts-container .post').length;
    $.ajax({
        url: "php_tool/postManager.php",
        type: 'GET',
        data: {
            echoLatestPosts: true,
            start: start,
        },
        success: function (response) {
            var responses = JSON.parse(response);
            for (rep of responses) {
                var element = document.querySelector('#posts-container');
                insertPost(rep, element, false, true);
            }
        }
    });
}

function ListPopularPosts() {
    start = $('#posts-container .post').length;
    $.ajax({
        url: "php_tool/postManager.php",
        type: 'GET',
        data: {
            echoPopularPosts: true,
            start: start,
        },  
        success: function (response) {
            var responses = JSON.parse(response);
            for (rep of responses) {
                var element = document.querySelector('#posts-container');
                insertPost(rep, element, false, true);
            }
        }
    });
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
                insertPost(rep, element, false, true);
            }
        }
    });
}

function ListFollowedPosts() {
    start = $('#posts-container .post').length;
    $.ajax({
        url: "php_tool/postManager.php",
        type: 'GET',
        data: {
            echoFollowedPosts: true,
            start: start,
        },
        success: function (response) {
            var responses = JSON.parse(response);
            var element = document.querySelector('#posts-container');
            if (responses.length === 0) {
                element.innerHTML = "<p>T'as pas d'amis sale merde.</p>";
            } else {
                for (rep of responses) {
                    insertPost(rep, element, false, true);
                }
            }
        }
    });
}

function ListPostByFilter(filter) {
    token = sessionStorage.getItem('token');
    switch (filter) {
        case 'Récents':
            ListLatestPosts();
            break;
        case 'Populaires':
            ListPopularPosts();
            break;
        case 'Découvertes':
            ListRandomPosts(token);
            break;
        case 'Suivis':
            ListFollowedPosts();
            break;
        default:
            ListRandomPosts(token);
            break;
    }
}

function clearPosts() {
    $('#posts-container').empty();
}

$(document).on('click', '.post, .like-button, [data-bs-toggle="modal"][data-bs-target="#modalPost"]', function () {
    var postId = $(this).data('post-id');

    if ($(this).hasClass('post')) {
        /* Open modal with responses */
        $('#modalResponses .modal-body').empty();

        var isOriginalPost = $(this).data('is-original-post') === true;
        var postParentId = $(this).data('post-id-parent');
        if (isOriginalPost && postParentId !== null) {
            postId = postParentId;
        }

        $.ajax({
            url: "php_tool/postManager.php",
            type: 'GET',
            data: {
                echoResponses: true,
                postId: postId,
            },
            success: function (response) {
                var responses = JSON.parse(response);
                var body_element = document.querySelector('#modalResponses .modal-body');
                var header_element = document.querySelector('#modalResponses .modal-header');
                header_element.innerHTML = '';
                insertPost(responses[0], header_element, true);

                if (responses[1] === undefined) {
                    body_element.innerHTML = "<h5 class='text-center'>Pas encore de réponse, soyez le premier à répondre !</h5>";
                }
                else {
                    for (var i = 1; i < responses.length; i++) {
                        insertPost(responses[i], body_element);
                    }
                }
            }
        });

        $('#modalResponses').modal('show');

    } else if ($(this).hasClass('like-button')) {
        /* Like button */
        var likeImage = $(`[data-like-image-for-post='${postId}']`);
        var likeCountElement = $(`[data-like-count-for-post='${postId}']`);

        $.ajax({
            url: 'php_tool/like_post.php',
            type: 'POST',
            data: {
                post_id: postId
            },
            success: function (response) {
                var data = JSON.parse(response);

                if (data.status === 'liked') {
                    likeImage.attr('src', '/WE4A_project/img/icon/liked.png');
                } else if (data.status === 'unliked') {
                    likeImage.attr('src', '/WE4A_project/img/icon/like.png');
                }

                likeCountElement.text(data.likeCount);
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
    let selectedFilter = localStorage.getItem('selectedFilter');


    if (sessionStorage.getItem('token')) {
        token = sessionStorage.getItem('token');
    } else {
        token = Math.floor(Math.random() * 100000);
        sessionStorage.setItem('token', token);
    }

    $('.dropdown-menu .dropdown-item').click(function() {
        let newSelectedFilter = $(this).text();
        if (newSelectedFilter !== selectedFilter) {
            clearPosts();
            ListPostByFilter(newSelectedFilter);
            selectedFilter = newSelectedFilter;
        }
    });


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
            let selectedFilter = localStorage.getItem('selectedFilter');
            ListPostByFilter(selectedFilter);
        }
    });
});