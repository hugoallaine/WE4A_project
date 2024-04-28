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
                        <img src='${post.avatar}' width='32' height='32' alt='Avatar' class='rounded-circle mr-2' style='object-fit: cover;'>
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
                                <button class='btn like-button' data-post-id='${post.id}'>
                                    <img src='${post.like_image}' alt='like button' class='img-fluid' >
                                </button>
                            </div>
                            <div class='col-12 p-0'>
                            <button class='btn' type='button' data-bs-toggle='modal' data-bs-target='#modalPost' data-tweet-id='${post.id}'>
                                <img src='/WE4A_project/img/icon/response.png' alt='response button' class='img-fluid'>
                            </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    element.innerHTML = html + element.innerHTML;
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
                for (rep of responses) {
                    var element = document.querySelector('#modalResponses .modal-body');
                    insertPost(rep, element);
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