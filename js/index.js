
function ListLatestPosts() {
    var start = $('#posts-container .post').length;
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
    var start = $('#posts-container .post').length;
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
    var start = $('#posts-container .post').length;
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
    var start = $('#posts-container .post').length;
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