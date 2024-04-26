function insertPost(post, element) {
    let pictureHtml = "";
    if (post.picture) {
        pictureHtml = `<a href='${post.picture}'><img src='${post.picture}' class='rounded' width='400' height='320' style='object-fit: cover;'></a>`;
    }

    var html = `
        <div class='card rounded-0' data-post-id='${post.id}'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-2 col-3 text-center'>
                        <a class='link-secondary link-underline link-underline-opacity-0' href='/WE4A_project/profile.php?pseudo=${post.pseudo}'>
                        <img src='${post.avatar}' width='32' height='32' alt='Avatar' class='rounded-circle mr-2' style='object-fit: cover;'>
                        <h5 class='card-title m-0'>${post.pseudo}</h5>
                        </a>
                        <p class='card-subtitle text-muted'>${post.date}</p>
                    </div>
                    <div class='col p-0'>
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



var buttons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#modalPost"]');
var allPosts = document.querySelectorAll('.post');

buttons.forEach(function (button) {
    button.addEventListener('click', function () {
        var tweetId = button.getAttribute('data-tweet-id');
        var input = document.querySelector('input[name="id_parent"]');
        input.value = tweetId;
    });
});

/* Echo response of a post */
allPosts.forEach(function(post) {
    post.addEventListener('click', function() {

        var postId = post.getAttribute('data-post-id');
        $('#modalResponses .modal-body').empty();

        $.ajax({
            url: "php_tool/postManager.php?echoResponses=true&postId="+postId,
            type: 'GET',
            data: {
                id: postId
            },
            success: function(response) {
                //Decode le json
                var responses = JSON.parse(response);
                console.log(responses);
                for (rep of responses) {
                    var element = document.querySelector('#modalResponses .modal-body');
                    insertPost(rep, element);
                }
            }
        });

        $('#modalResponses').modal('show');

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