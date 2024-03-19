<?php
require_once dirname(__FILE__).'/vendor/autoload.php';

// Inclure les dépendances Google Cloud à l'aide de Composer
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

/**
  * Créez une évaluation pour analyser le risque d'une action dans l'interface utilisateur.
  * @param string $recaptchaKey La clé reCAPTCHA associée au site ou à l'application
  * @param string $token Jeton généré auprès du client.
  * @param string $project L'ID de votre projet Google Cloud.
  * @param string $action Nom d'action correspondant au jeton.
  */
function create_assessment(
  string $recaptchaKey,
  string $token,
  string $project,
  string $action
): void {
  // Créez le client reCAPTCHA.
  // À FAIRE : mettre en cache le code de génération du client (recommandé) ou appeler client.close() avant de quitter la méthode.
  $client = new RecaptchaEnterpriseServiceClient();
  $projectName = $client->projectName($project);

  // Définissez les propriétés de l'événement à suivre.
  $event = (new Event())
    ->setSiteKey($recaptchaKey)
    ->setToken($token);

  // Créez la demande d'évaluation.
  $assessment = (new Assessment())
    ->setEvent($event);

  try {
    $response = $client->createAssessment(
      $projectName,
      $assessment
    );

    // Vérifiez si le jeton est valide.
    if ($response->getTokenProperties()->getValid() == false) {
      printf('The CreateAssessment() call failed because the token was invalid for the following reason: ');
      printf(InvalidReason::name($response->getTokenProperties()->getInvalidReason()));
      return;
    }

    // Vérifiez si l'action attendue a été exécutée.
    if ($response->getTokenProperties()->getAction() == $action) {
      // Obtenez le score de risques et le ou les motifs.
      // Pour savoir comment interpréter l'évaluation, consultez les pages suivantes :
      // https://cloud.google.com/recaptcha-enterprise/docs/interpret-assessment
      printf('The score for the protection action is:');
      printf($response->getRiskAnalysis()->getScore());
    } else {
      printf('The action attribute in your reCAPTCHA tag does not match the action you are expecting to score');
    }
  } catch (exception $e) {
    printf('CreateAssessment() call failed with the following error: ');
    printf($e);
  }
}

// À FAIRE : remplacer le jeton et les variables d'action reCAPTCHA avant d'exécuter l'exemple.
create_assessment(
   '6Le1YZ4pAAAAAJRKez380JQpQuUFqPwzT-NxW0a7',
   'YOUR_USER_RESPONSE_TOKEN',
   'ygreg-1710881084848',
   'YOUR_RECAPTCHA_ACTION'
);
?>