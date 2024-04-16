<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
redirectIfNotConnected();

$currentPage = 'Paramètres';

require_once dirname(__FILE__).'/php_tool/db.php';
require_once dirname(__FILE__).'/php_tool/vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;

if (!isTfaEnabled()) {
    $tfa = new TwoFactorAuth();
    $tfa_code = $tfa->createSecret();
}


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
                    <?php if(isTfaEnabled()){echo "Désactiver";}else{echo "Activer";} ?> la double authentification
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionSettings">
                <div class="accordion-body">
                    <?php if(isTfaEnabled()): ?>
                        <p>Vous avez activé la double authentification.</p>
                        <form id="formDisableTfa" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="password_check" name="password_check" placeholder="Mot de passe" required>
                                <label for="password_check">Mot de passe</label>
                            </div>
                            <button type="submit" class="btn btn-danger">Désactiver la double authentification</button>
                        </form>
                    <?php else: ?>
                        <p>Scanner le QRCode ou taper le code secret manuellement dans votre application de double authentification. <span class="text-secondary">Exemple: Google Authenticator, Microsoft Authenticator, etc...</span></p>
                        <div class="card mb-3" style="width: 18rem;">
                            <img src="<?= $tfa->getQRCodeImageAsDataUri('Jeu2Dame.tk', $tfa_code) ?>" class="rounded">
                            <div class="card-body">
                                <h5 class="card-title text-center"><?php echo $tfa_code ?></h5>
                            </div>
                        </div>
                        <form id="formEnableTfa" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="tfa_code" name="tfa_code" placeholder="Code de vérification" required>
                                <label for="tfa_code">Code de vérification</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password_check_tfa" name="password_check_tfa" placeholder="Mot de passe" required>
                                <label for="password_check_tfa">Mot de passe</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Activer la double authentification</button>
                        </form>
                    <?php endif; ?>
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
                    <p>Attention, cette action est irréversible.</p>
                    <form id="formDeleteAccount" method="POST">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password_check_delete" name="password_check_delete" placeholder="Mot de passe" required>
                                <label for="password_check_delete">Mot de passe</label>
                            </div>
                            <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>