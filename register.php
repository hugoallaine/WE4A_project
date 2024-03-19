<?php

?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>YGreg - Register</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='css/default.css'>
    <link rel='stylesheet' type='text/css' href='css/login.css'>
</head>
<body>
    <section>
        <form method="POST" action="">
            <h1>Inscription à YGreg</h1>
            <div>
                <input type="text" class="form_input" name="user" placeholder="" required/>
                <label>Adresse Mail</label>
            </div>
            <div>
                <input type="password" class="form_input" name="password" placeholder="" required/>
                <label>Mot de passe</label>
            </div>
            <div>
                <input type="password" class="form_input" name="password" placeholder="" required/>
                <label>Confirmer le mot de passe</label>
            </div>
            <input type="submit" class="form_submit" name="form" value="S'inscrire"/>
            <div class=error-message><?php if(isset($error)){echo '<p>'.$error."</p>";} ?></div>
        </form>
    </section>
</body>
</html>