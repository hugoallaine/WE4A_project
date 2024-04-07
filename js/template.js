function TogglePassword(checked) {
    var x = document.getElementById("password");
    if (checked) {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

$(document).ready(function(){
    $('.textAreaPost').on('keydown', function(e) {
        // Si la touche pressée est Entrée
        if (e.which == 13) {
            e.preventDefault();
            $('#formPostId').submit();
        }
    });

    /* Envoi du formulaire d'ajout de post */
    $('#formPostId').submit(function(e){
        e.preventDefault(); // Empêcher le formulaire de se soumettre normalement
        
        // Récupérer les données du formulaire
        var formData = $(this).serialize();
        // Envoyer les données du formulaire via AJAX
        $.ajax({
            type: 'POST',
            url: 'php_tool/submit_post.php',
            data: formData,
            success: function(response){
                document.getElementById('formPostId').reset();
                $('#modalPost').modal('hide');
            }
        });
    });

    /* Login */
    $('#formLoginId').submit(function(e){
        e.preventDefault(); // Empêcher le formulaire de se soumettre normalement
        
        // Récupérer les données du formulaire
        var formData = $(this).serialize();
        // Envoyer les données du formulaire via AJAX
        $.ajax({
            type: 'POST',
            url: 'php_tool/main_login.php',
            data: formData,
            success: function(response){
                if (response.error) {
                    $('#error_message').text(response.message);
                } else {
                    $('#modalLogin').modal('hide');
                    var loginToast = new bootstrap.Toast(document.getElementById('loginToast'));
                    loginToast.show();
                    $('#section-to-refresh').html(response);
                }
                document.getElementById('formLoginId').reset();
            },
        });
    });

    /* Logout */
    $('#logout-button').click(function(e){
        e.preventDefault();
    
        $.ajax({
            type: 'POST',
            url: 'php_tool/logout.php',
            success: function(){
                location.reload();
            },
            error: function(){

                console.log('Erreur lors de la déconnexion');
            }
        });
    });
});