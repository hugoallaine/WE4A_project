<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
redirectIfNotConnected();
require_once dirname(__FILE__).'/php_tool/db.php';

$currentPage = 'Paramètres';

require_once dirname(__FILE__).'/php_tool/template_top.php';
?>
<main>
    <div class="p-3">
        <h1 class="text-primary">Paramètres</h1>
    </div>
    <div class="accordion" id="accordionSettings">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Changer de mot de passe
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionSettings">
                <div class="accordion-body">
                    <form id="formChangePassword" method="POST">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="oldPassword" name="oldPassword" placeholder="Ancien mot de passe" required>
                                <label for="oldPassword">Ancien mot de passe</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Nouveau mot de passe" required>
                                <label for="newPassword">Nouveau mot de passe</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="newPasswordConfirm" name="newPasswordConfirm" placeholder="Répéter le nouveau mot de passe" required>
                                <label for="newPasswordConfirm">Confirmer le nouveau mot de passe</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Valider</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <?php if($isTfaEnabled){echo "Désactiver";}else{echo "Activer";} ?> la double authentification
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionSettings">
                <div class="accordion-body">
                    <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Supprimer mon compte
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionSettings">
                <div class="accordion-body">
                    <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                </div>
            </div>
        </div>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>