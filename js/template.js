/**
 * Insert a post in the DOM
 * @param {*} post 
 * @param {*} element 
 * @param {*} isOriginalPost 
 * @param {*} insertAfter 
 */
function insertPost(post, element, isOriginalPost = false, insertAfter = false) {

    if(post.is_removed && !window.location.pathname.includes("notifications")) {
        return;
    }

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
                        <a class='link-secondary link-underline link-underline-opacity-0' href='profile.php?pseudo=${post.pseudo}'>
                        <img src='${post.avatar}' width='64' height='64' alt='Avatar' class='rounded-circle mr-2' style='object-fit: cover;'/>
                        <h5 class='card-title m-0'>${post.pseudo}</h5>
                        ${post.isAdmin ? `<span class="badge bg-danger m-2">Admin</span>`: ``}
                        ${post.isBan ? `<span class="badge bg-danger m-2">Banni</span>`: ``}
                        </a>
                        <p class='card-subtitle text-muted'>${post.date}</p>
                        ${(sessionStorage.getItem('isAdmin') === 'true') ? `<button class='btn dropdown-toggle pt-3' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                                        <img src='img/icon/administrateur.png' width='48' height='48' alt='admin button' class='img-fluid'/>
                                      </button>
                                      <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                        <li><a class='dropdown-item' href='#' onclick='setAdminModal("warning",${post.id})' data-bs-toggle='modal' data-bs-target='#modalAdmin'>Envoyer un avertissement</a></li>
                                        <li><a class='dropdown-item' href='#' onclick='setAdminModal("shock",${post.id})' data-bs-toggle='modal' data-bs-target='#modalAdmin'>Marquer comme choquant</a></li>
                                        <li><a class='dropdown-item' href='#' onclick='setAdminModal("delete",${post.id})' data-bs-toggle='modal' data-bs-target='#modalAdmin'>Supprimer le Greg</a></li>
                                        <li><a class='dropdown-item' href='#' onclick='setAdminModal("ban",${post.id})' data-bs-toggle='modal' data-bs-target='#modalAdmin'>Bannir l'utilisateur</a></li>
                                      </ul>` : ``}
                    </div>
                    <div class='col p-0 post' style='cursor: pointer; ${post.is_sensible && !sessionStorage.getItem(post.id) ? "filter: blur(16px);" : "" } ' data-post-id='${post.id}' data-post-id-parent='${post.id_parent}' ${isOriginalPost ? "data-is-original-post='true'" : ""} ${post.is_sensible ? "onclick='removeBlur(this)'" : ""}>
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
                                    <img src='img/icon/response.png' alt='response button' class='img-fluid'/>
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
 * Switch the theme of the website
 */
function switchTheme() {
    let theme = sessionStorage.getItem('theme');
    if (theme === 'light-theme') {
        sessionStorage.setItem('theme', 'dark-theme');
        $('body').addClass('dark-theme');
        $('icon-theme').attr('src', 'img/icon/sunny.png');
    } else {
        sessionStorage.setItem('theme', 'light-theme');
        $('body').removeClass('dark-theme');
        $('icon-theme').attr('src', 'img/icon/moon.png');
    }
}

/**
 * Display the number of unread notifications
 */
function checkNotificationsNumberUnread() {
    let nbNotif = document.getElementById("nbNotif");
    $.ajax({
        type: 'GET',
        url: 'php/notificationManager.php?count=true',
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
            let img_path = user.avatar ? 'img/user/'+user.id+'/'+user.avatar : 'img/icon/utilisateur.png';
            var resultElement = ('<div><a href="profile.php?pseudo='+ user.pseudo +'" class="link link-primary link-underline-opacity-0 ms-3"><img src="'+img_path+'" alt="Icon User" class="rounded-circle object-fit-cover me-2 mb-2" width="32" height="32"/>'+ user.pseudo +'</a></div>');
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


function removeBlur(element) {
    element.style.filter = 'none';
    sessionStorage.setItem(element.dataset.postId, 'true');
}

/**
 * Set the modal for the admin actions
 * @param {*} type 
 * @param {*} postId 
 */
function setAdminModal(type, postId) {
    if (type === 'warning') {
        $('#modalAdminLabel').text("Envoyer un avertissement");
        $('#notif-message').val('Vous avez reçu un avertissement d\'un administrateur pour le Greg suivant : ');
        $('#admin-action-type').val('warning');
        $("#ban-date-div").empty();
    } else if (type === 'shock') {
        $('#modalAdminLabel').text("Marquer comme choquant");
        $('#notif-message').val('Le Greg suivant a été marqué comme choquant par un administrateur : ');
        $('#admin-action-type').val('shock');
        $("#ban-date-div").empty();
    } else if (type === 'delete') {
        $('#modalAdminLabel').text("Supprimer le Greg");
        $('#notif-message').val('Le Greg suivant a été supprimé par un administrateur : ');
        $('#admin-action-type').val('delete');
        $("#ban-date-div").empty();
    } else if (type === 'ban') {
        $('#modalAdminLabel').text("Bannir l'utilisateur");
        $('#notif-message').val('Vous avez été banni par un administrateur pour le Greg suivant : ');
        $('#admin-action-type').val('ban');
        $("#ban-date-div").empty();
        let dateLabel = $("<label>");
        dateLabel.attr("for", "ban-time");
        dateLabel.attr("class", "form-label mt-2");
        dateLabel.text("Date de fin du bannissement :");
        $("#ban-date-div").append(dateLabel);
        let dateInput = $("<input>");
        dateInput.attr("id", "ban-time");
        dateInput.attr("type", "datetime-local");
        dateInput.attr("class", "form-control mb-2")
        dateInput.attr("name", "banTime");
        $("#ban-date-div").append(dateInput);
        let checkBanDef = $("<input>");
        checkBanDef.attr("type", "checkbox");
        checkBanDef.attr("id", "ban-def");
        checkBanDef.attr("name", "banDef");
        checkBanDef.attr("class", "form-check-input me-2");
        $("#ban-date-div").append(checkBanDef);
        let checkLabel = $("<label>");
        checkLabel.attr("for", "ban-def");
        checkLabel.attr("class", "form-check-label");
        checkLabel.text("Bannissement définitif");
        $("#ban-date-div").append(checkLabel);
    }
    $('#admin-post-control-id').val(postId);
}

/**
 * Search bar
 */
$('#search-bar').on('input', function() {
    let searchTerm = $(this).val();
    if (searchTerm.length >= 1) {
        $.ajax({
            type: 'GET',
            url: 'php/search.php',
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
    /* Check the theme for icon */
    let theme = sessionStorage.getItem('theme');
    if (theme === null) {
        sessionStorage.setItem('theme', 'light-theme');
    }
    if (theme === 'dark-theme') {
        switchTheme();
    }

    /* Check if user is connected and if is an admin */
    $.ajax({
        url: "php/checkSession.php",
        type: 'GET',
        success: function (response) {
            if (response.status === true) {
                sessionStorage.setItem('isConnected', true);
                sessionStorage.setItem('pseudo', response.pseudo);
                if (response.isAdmin === 1) {
                    sessionStorage.setItem('isAdmin', true);
                }
            } else {
                sessionStorage.setItem('isConnected', false);
                sessionStorage.setItem('isAdmin', false);
                
            }
        }
    });

    /* Envoi du formulaire d'ajout de post et affichage du post ajouté */
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
        /* Check if the image is too big */
        let file = formData.get('images');
        if (file.size > 2097152) {
            let errorDiv = $('.modal-body').find('.alert-danger');
            if (errorDiv.length === 0) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-2 mb-0';
                errorDiv.textContent = 'L\'image ne peut pas dépasser 2 Mo.';
                $('.modal-body').append(errorDiv);
            }
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: 'php/postManager.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                response = JSON.parse(response);
                document.getElementById('formPostId').reset();
                $('#modalPost').modal('hide');
                if (document.title.includes("Accueil") || ($('#pseudo').text() === sessionStorage.getItem('pseudo')) && sessionStorage.getItem('selectedFilter') === 'Gregs') {
                    insertPost(response, document.querySelector('#posts-container'));
                    console.log("Post ajouté");
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
            url: 'php/login.php',
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
            url: 'php/register.php',
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
            url: 'php/logout.php',
            success: function () {
                location.reload();
            },
        });
    });

    /* Advertissement */
    $('#formAdmin').submit(function (e) {
        e.preventDefault();
        if ($('#notif-message').val().length === 0) {
            $('#error-message-admin').text('Veuillez entrer un message.');
            return;
        }
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php/administrator.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message-admin').text(response.message);
                } else {
                    $('#modalAdmin').modal('hide');
                    let notifToast = new bootstrap.Toast(document.getElementById('sendNotifToast'));
                    notifToast.show();
                }
                document.getElementById('formAdmin').reset();
            }
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
            url: "php/postManager.php",
            type: 'GET',
            data: {
                command: 'echoResponses',
                postId: postId,
            },
            success: function (response) {
                var responses = JSON.parse(response);
                var body_element = document.querySelector('#modalResponses .modal-body');
                var header_element = document.querySelector('#modalResponses .modal-header');
                header_element.innerHTML = '';
                insertPost(responses[0], header_element, true);

                if (responses[0].is_removed) {
                    header_element.innerHTML = "<div class='w-100'><h5 class='text-center'>Ce Greg a été supprimé.</h5></div>";
                }

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
            url: 'php/like_post.php',
            type: 'POST',
            data: {
                post_id: postId
            },
            success: function (response) {
                var data = JSON.parse(response);

                if (data.status === 'liked') {
                    likeImage.attr('src', 'img/icon/liked.png');
                } else if (data.status === 'unliked') {
                    likeImage.attr('src', 'img/icon/like.png');
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