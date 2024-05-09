function changeText() {
    let btn = document.getElementById("btnFollow");
    if (btn.textContent == "Suivi") {
        btn.textContent = "Ne plus suivre";
        btn.classList.remove("btn-primary");
        btn.classList.add("btn-secondary");
    }
}

function resetText() {
    let btn = document.getElementById("btnFollow");
    if (btn.textContent == "Ne plus suivre") {
        btn.textContent = "Suivi";
        btn.classList.remove("btn-secondary");
        btn.classList.add("btn-primary");
    }
}

function ListOnProfileAllGreg() {
    var start = $('#posts-container .post').length;
    $.ajax({
        url: "php/postManager.php",
        type: 'GET',
        data: {
            command : 'echoProfileAllGreg',
            start: start,
            userIdOfProfileViewed: parseInt(sessionStorage.getItem('userIdOfProfileViewed')),
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

function ListOnProfileAllResponse() {
    var start = $('#posts-container .post').length;
    $.ajax({
        url: "php/postManager.php",
        type: 'GET',
        data: {
            command: 'echoProfileAllResponse',
            start: start,
            userIdOfProfileViewed: parseInt(sessionStorage.getItem('userIdOfProfileViewed')),
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

function ListOnProfileAllLikes() {
    var start = $('#posts-container .post').length;
    $.ajax({
        url: "php/postManager.php",
        type: 'GET',
        data: {
            command : 'echoProfileAllLikes',
            start: start,
            userIdOfProfileViewed: parseInt(sessionStorage.getItem('userIdOfProfileViewed')),
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

/**
 * List posts by filter
 * @param {*} filter 
 */
function ListPostByFilter(filter) {
    switch (filter) {
        case 'Gregs':
            ListOnProfileAllGreg();
            break;
        case 'Réponses':
            ListOnProfileAllResponse();
            break;
        case 'Likes':
            ListOnProfileAllLikes();
            break;
        default:
            ListOnProfileAllGreg();
            break;
    }
}


/**
 * Clear posts
 */
function clearPosts() {
    $('#posts-container').empty();
}

$(document).ready(function () {
    /* Get ID of the user viewed */
    var userId = $('span#pseudo').data('user-id');
    sessionStorage.setItem('userIdOfProfileViewed', userId);

    ListOnProfileAllGreg();

    /* Modifier le profil */
    $('#formProfile').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: 'php/changeAccount.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    $('#modalProfile').modal('hide');
                    let profileToast = new bootstrap.Toast(document.getElementById('changeProfileToast'));
                    profileToast.show();
                    setTimeout(function () {
                        if (response.changedpseudo) {
                            window.location.href = "profile.php?pseudo=" + response.pseudo;
                        } else {
                            location.reload(true);
                        }
                    }, 2000);
                }
                document.getElementById('formProfile').reset();
            }
        });
    });

    /* Suivre un utilisateur */
    $('#formFollow').submit(function (e) {
        e.preventDefault();
        if ($('#btnFollow').text() == "Ne plus suivre") {
            let confirmation = confirm("Êtes-vous sûr de vouloir ne plus suivre cet utilisateur ?");
            if (!confirmation) {
                return;
            }
        }
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php/follow.php',
            data: formData,
            success: function (response) {
                if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    if (response.message == 'followed') {
                        $('#btnFollow').text("Suivi");
                        $('#nbFollowers').text(parseInt($('#nbFollowers').text()) + 1);
                    } else {
                        $('#btnFollow').text("Suivre");
                        $('#nbFollowers').text(parseInt($('#nbFollowers').text()) - 1);
                    }
                }
            }
        });
    });

    /* Gestion follow */
    $('.btnUnfollow').click(function () {
        var btn = $(this);
        $.ajax({
            type: 'POST',
            url: 'php/follow.php',
            data: {
                id: $(this).attr('user-id'),
            },
            success: function (response) {
                if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    if (response.message == 'followed') {
                        btn.text("Se désabonner");
                        btn.removeClass("btn-primary");
                        btn.addClass("btn-secondary");
                        $('#nbFollowing').text(parseInt($('#nbFollowing').text()) + 1);
                    } else {
                        btn.text("Suivre");
                        btn.removeClass("btn-secondary");
                        btn.addClass("btn-primary");
                        $('#nbFollowing').text(parseInt($('#nbFollowing').text()) - 1);
                    }
                }
            }
        });
    });

    let selectedFilter = sessionStorage.getItem('selectedFilter');

    $('.dropdown-menu .dropdown-item').click(function() {
        var newSelectedFilter = $(this).text();
        if (newSelectedFilter === 'Suivis' && sessionStorage.getItem('isConnected') === 'false') {
            $('#modalLogin').modal('show');
        } else if (newSelectedFilter !== selectedFilter) {
            clearPosts();
            selectedFilter = newSelectedFilter;
            sessionStorage.setItem('selectedFilter', selectedFilter);
            ListPostByFilter(selectedFilter);
        }
    });

    /* Load more posts */
    $('#profile-view').on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight && $('#profile-view .post').length > 0){
            let selectedFilter = sessionStorage.getItem('selectedFilter');
            ListPostByFilter(selectedFilter);
        }
    });
});