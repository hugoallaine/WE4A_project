
/**
 * Insert a post in the DOM
 * @param {*} post 
 * @param {*} element 
 * @param {*} isOrginalPost 
 * @param {*} insertAfter 
 */
function insertPost(post, element, isOrginalPost = false, insertAfter = false) {

    let pictureHtml = "";
    let isConnected = sessionStorage.getItem('isConnected');
    isConnected == 'true' ? isConnected = true : isConnected = false;

    if (post.picture) {
        pictureHtml = `<a href='${post.picture}'><img src='${post.picture}' class='rounded' width='400' height='320' style='object-fit: cover;'></a>`;
    }

    var html = `
        <div class='card rounded-0'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-2 col-3 text-center'>
                        <a class='link-secondary link-underline link-underline-opacity-0' href='/WE4A_project/profile.php?pseudo=${post.pseudo}'>
                        <img src='${post.avatar}' width='64' height='64' alt='Avatar' class='rounded-circle mr-2' style='object-fit: cover;'/>
                        <h5 class='card-title m-0'>${post.pseudo}</h5>
                        ${post.isAdmin ? `<span class="badge bg-danger m-2">Admin</span>`: ``}
                        </a>
                        <p class='card-subtitle text-muted'>${post.date}</p>
                        ${(sessionStorage.getItem('isAdmin') === 'true') ? `<button class='btn dropdown-toggle pt-3' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                                        <img src='/WE4A_project/img/icon/administrateur.png' width='48' height='48' alt='admin button' class='img-fluid'/>
                                      </button>
                                      <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                        <li><a class='dropdown-item' href='#'>Envoyer un avertissement</a></li>
                                        <li><a class='dropdown-item' href='#'>Marquer comme choquant</a></li>
                                        <li><a class='dropdown-item' href='#'>Supprimer le Greg</a></li>
                                        <li><a class='dropdown-item' href='#'>Bannir l'utilisateur</a></li>
                                      </ul>` : ``}
                    </div>
                    <div class='col p-0 post' style='cursor: pointer;' data-post-id='${post.id}' data-post-id-parent='${post.id_parent}' ${isOrginalPost ? "data-is-original-post='true'" : ""}>
                        <p>${post.content}</p>
                        ${pictureHtml}    
                    </div>
                    <div class='col-1'>
                        <div class='row'>
                            <div class='col-12 p-0'>
                                <button class='btn ${isConnected ? `like-button` : ``}' ${isConnected ? `data-post-id='${post.id}'` : `data-bs-toggle='modal' data-bs-target='#modalLogin'`}>
                                    <img data-like-image-for-post='${post.id}' src='${post.like_image}' alt='like button' class='img-fluid' >
                                </button>
                            </div>
                            <div class='col-12 p-0 mb-2 text-center'>
                                <strong data-like-count-for-post='${post.id}'>${post.like_count}</strong>
                            </div>
                            <div class='col-12 p-0'>
                                <button class='btn' type='button' ${isConnected ? `data-bs-toggle='modal' data-bs-target='#modalPost' data-tweet-id='${post.id}'` : `data-bs-toggle='modal' data-bs-target='#modalLogin'`}>
                                    <img src='/WE4A_project/img/icon/response.png' alt='response button' class='img-fluid'/>
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

/**
 * Toggle password visibility in login form
 * @param {*} checked 
 */
function TogglePassword(checked) {
    let x = document.getElementById("password");
    if (checked) {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

/**
 * Toggle password visibility in register form
 * @param {*} checked 
 */
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

/**
 * Display the number of unread notifications
 */
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

/**
 * Display search results
 * @param {*} query 
 */
function displayResults(query) {
    $('#search-results').empty();
    if (query.users.length > 0) {
        $('#search-results').append('<h6 class="text-center">Utilisateurs</h6>');
        $.each(query.users, function(index, user) {
            var resultElement = ('<div><a href="profile.php?pseudo='+ user.pseudo +'" class="link link-primary link-underline-opacity-0 ms-3"><img src="img/user/'+ user.id +'/'+ user.avatar +'" alt="Icon User" class="rounded-circle object-fit-cover me-2 mb-2" width="32" height="32"/>'+ user.pseudo +'</a></div>');
            $('#search-results').append(resultElement);
        });
    }
    if (query.posts.length > 0) {
        $('#search-results').append('<h6 class="text-center">Gregs</h6>');
        $.each(query.posts, function(index, post) {
            var resultElement = ('<div class="post" data-post-id="'+ post.id +'" style="cursor: pointer;"><p class="ms-3 me-3"><a href="profile.php?pseudo='+ post.pseudo +'" class="link link-primary link-underline-opacity-0">'+ post.pseudo +'</a> : '+ post.content +'</p></div>');
            $('#search-results').append(resultElement);
        });
    }
    $('#search-results').show();
}

/**
 * Search bar
 */
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

/**
 * Hide search results when clicking outside the search bar
 */
$(document).on('click', function(e) {
    if (!$(e.target).closest('.search-container').length) {
        $('#search-results').hide();
    }
});

$(document).ready(function () {
    
    /* Check if user is connected and if is an admin */
    $.ajax({
        url: "php_tool/checkSession.php",
        type: 'GET',
        success: function (response) {
            if (response.status === true) {
                sessionStorage.setItem('isConnected', true);
                if (response.isAdmin === 1) {
                    sessionStorage.setItem('isAdmin', true);
                }
            } else {
                sessionStorage.setItem('isConnected', false);
                sessionStorage.setItem('isAdmin', false);
                
            }
        }
    });



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

/**
 * Manage the display of responses, display of likes and add trigger to the modal
 */
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