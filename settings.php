<?php
require_once dirname(__FILE__).'/php/alreadyConnected.php';
session_start_secure();
redirectIfNotConnected();

$currentPage = 'Paramètres';

require_once dirname(__FILE__).'/php/db.php';
$req = $db->prepare('SELECT u.name,u.firstname,u.birth_date,a.address,a.city,a.zip_code,a.country FROM users u JOIN address a ON u.id = a.id_user WHERE email = ?');
$req->execute([$_SESSION['email']]);
$userinfo = $req->fetch();
$formattedFirstname = RestoreString_FromSQL($userinfo['firstname']);
$formattedName = RestoreString_FromSQL($userinfo['name']);
$formattedDate = date("Y-m-d", strtotime($userinfo['birth_date']));
$formattedAddress = RestoreString_FromSQL($userinfo['address']);
$formattedCity = RestoreString_FromSQL($userinfo['city']);
$formattedCountry = RestoreString_FromSQL($userinfo['country']);

require_once dirname(__FILE__).'/php/toast.php';
require_once dirname(__FILE__).'/php/vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;

if (!isTfaEnabled()) {
    $tfa = new TwoFactorAuth($issuer = 'YGreg');
    $tfa_code = $tfa->createSecret();
}

require_once dirname(__FILE__).'/php/template_top.php';
?>
<main>
    <div class="vh-100 overflow-auto">
        
    <div class="p-3">
        <h1 class="text-primary">Paramètres</h1>
    </div>
    <div class="accordion" id="accordionSettings">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseZero" aria-expanded="true" aria-controls="collapseZero">
                    Changer mes informations personnelles
                </button>
            </h2>
            <div id="collapseZero" class="accordion-collapse collapse show" data-bs-parent="#accordionSettings">
                <div class="accordion-body">
                    <form id="formChangeInfo" method="POST">
                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="firstname" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $formattedFirstname; ?>" autocomplete="given-name">
                                </div>
                                <div class="col">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $formattedName; ?>" autocomplete="family-name">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="birthdate" class="form-label">Date de naissance</label>
                                <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $formattedDate; ?>" autocomplete="bday">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label for="inputAddress" class="form-label">Adresse</label>
                                    <input type="text" class="form-control" id="inputAddress" name="address" value="<?php echo $formattedAddress; ?>" autocomplete="street-address">
                                </div>
                                <div class="col-md-4">
                                    <label for="inputCity" class="form-label">Ville</label>
                                    <input type="text" class="form-control" id="inputCity" name="city" value="<?php echo $formattedCity; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="inputZip" class="form-label">Code Postal</label>
                                    <input type="text" class="form-control" id="inputZip" name="zipcode" value="<?php echo $userinfo['zip_code']; ?>" autocomplete="postal-code">
                                </div>
                                <div class="col-md-4">
                                    <label for="inputCountry" class="form-label">Pays</label>
                                    <input type="text" class="form-control" id="inputCountry" name="country" value="<?php echo $formattedCountry; ?>" autocomplete="country-name">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Valider</button>
                            <div id="error-message-info" class="text-danger text-align-center"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    Changer de mot de passe
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionSettings">
                <div class="accordion-body">
                    <form id="formChangePassword" method="POST">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="oldPassword" name="oldPassword" placeholder="" required>
                                <label for="oldPassword">Ancien mot de passe</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="" required>
                                <label for="newPassword">Nouveau mot de passe</label>
                                <div class="form-text">
                                    Les caractéristiques minimales pour un mot de passe sont :
                                    <ul>
                                        <li>12 caractères</li>
                                        <li>1 majuscule</li>
                                        <li>1 chiffre</li>
                                        <li>1 caractère spécial</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="newPasswordConfirm" name="newPasswordConfirm" placeholder="" required>
                                <label for="newPasswordConfirm">Confirmer le nouveau mot de passe</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Valider</button>
                            <div id="error-message-password" class="text-danger text-align-center"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Double authentification (2FA) <?php if(isTfaEnabled()){echo '<span class="badge text-bg-success ms-2">Actif</span>';}else{echo '<span class="badge text-bg-danger ms-2">Inactif</span>';} ?>
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionSettings">
                <div class="accordion-body">
                    <?php if(isTfaEnabled()): ?>
                        <p>La double authentification est activée sur votre compte.</p>
                        <form id="formDisableTfa" method="POST">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password_check" name="password_check" placeholder="" required>
                                <label for="password_check">Mot de passe</label>
                            </div>
                            <button type="submit" class="btn btn-danger">Désactiver la double authentification</button>
                            <div id="error-message" class="text-danger text-align-center"></div>
                        </form>
                    <?php else: ?>
                        <p>Scanner le QRCode ou taper le code secret manuellement dans votre application de double authentification. <span class="text-secondary">Exemple: Google Authenticator, Microsoft Authenticator, etc...</span></p>
                        <div class="row">
                            <div class="col-3">
                                <div class="card mb-3">
                                    <img src="<?= $tfa->getQRCodeImageAsDataUri($_SESSION['email'], $tfa_code) ?>" class="rounded">
                                    <div class="card-body">
                                        <h5 class="card-title text-center"><?php echo $tfa_code ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-9">
                                <form id="formEnableTfa" method="POST">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="tfa_code" name="tfa_code" placeholder="" required>
                                        <label for="tfa_code">Code de vérification</label>
                                        <div class="form-text">
                                            Insérer le code généré par l'application.
                                        </div>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password_check_tfa" name="password_check_tfa" placeholder="" required>
                                        <label for="password_check_tfa">Mot de passe</label>
                                        <div class="form-text">
                                            Merci de renseigner votre mot de passe pour vérifier votre identité.
                                        </div>
                                    </div>
                                    <input type="hidden" name="tfa_secret" value="<?= $tfa_code ?>">
                                    <button type="submit" class="btn btn-primary">Activer la double authentification</button>
                                    <div id="error-message" class="text-danger text-align-center"></div>
                                </form>
                            </div>
                        </div>
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
                                <input type="password" class="form-control" id="password_check_delete" name="password_check_delete" placeholder="" required>
                                <label for="password_check_delete">Mot de passe</label>
                            </div>
                            <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php 
    echo '<div class="toast-container position-fixed bottom-0 end-0 m-3">';
    if (!isTfaEnabled()) {
        successToast("La double authentification a bien été activée.", "activationTfaToast");
    } else {
        successToast("La double authentification a bien été désactivée.", "desactivationTfaToast");
    }
    successToast("Vos informations personnelles ont bien été modifiées.", "changeInfoToast");
    successToast("Votre mot de passe a bien été modifié.", "changePasswordToast");
    successToast("Votre compte a bien été supprimé.", "deleteAccountToast");
    echo '</div>';
    ?>
</main>
<?php
require_once dirname(__FILE__).'/php/template_bot.php';
?>