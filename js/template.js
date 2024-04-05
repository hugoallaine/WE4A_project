$(document).ready(function(){
    $('.textAreaPost').on('keydown', function(e) {
        // Si la touche pressée est Entrée
        if (e.which == 13) {
            e.preventDefault();
            $('#formPostId').submit();
        }
    });

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
                document.getElementById('modalPost').style.display = 'none';
                document.getElementById('formPostId').reset();
                $('#modalPost').modal('hide');
            }
        });
    });
});