
function listPosts(command, token = null) {
    var start = $('#posts-container .post').length;
    $.ajax({
        url: "php/postManager.php",
        type: 'GET',
        data: {
            command: command,
            start: start,
            token: token
        },
        success: function (response) {
            var responses = JSON.parse(response);
            var element = document.querySelector('#posts-container');
            if (responses.length === 0 && command === 'echoFollowedPosts') {
                element.innerHTML = "<h2 class='text-center'>Vous ne suivez personne</h2>";
            } else {
                for (rep of responses) {
                    insertPost(rep, element, false, true);
                }
            }
        }
    });
}

function ListLatestPosts() {
    listPosts('echoLatestPosts');
}

function ListPopularPosts() {
    listPosts('echoPopularPosts');
}

function ListRandomPosts() {
    listPosts('echoRandomPosts', sessionStorage.getItem('token'));
}

function ListFollowedPosts() {
    listPosts('echoFollowedPosts');
}

/**
 * List posts by filter
 * @param {*} filter 
 */
function ListPostByFilter(filter) {
    var token = sessionStorage.getItem('token');
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

/**
 * Clear posts
 */
function clearPosts() {
    $('#posts-container').empty();
}

$(document).ready(function () {

    let token;
    let selectedFilter = sessionStorage.getItem('selectedFilter');

    /* Generate token or get it from session storage */
    if (sessionStorage.getItem('token')) {
        token = sessionStorage.getItem('token');
    } else {
        token = Math.floor(Math.random() * 100000);
        sessionStorage.setItem('token', token);
    }

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

    /* Load posts */
    ListPostByFilter(selectedFilter);

    /* Load more posts */
    $('#posts-container').on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight && $('#posts-container .post').length > 0){
            let selectedFilter = sessionStorage.getItem('selectedFilter');
            ListPostByFilter(selectedFilter);
        }
    });
});