<?php
session_start();
$_SESSION['login'] = $pseudo;
$_SESSION['pass'] = $pass;
?>
<!doctype html>
<html lang="fr">
    
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">

        <title>Les P'tits Gones - Accueil</title>
        
        <link rel="stylesheet" href="css/index_style.css">
        
        <script src="js/jquery_error.js"></script>

    </head>

    <body>
        <div class="container">
            <p> Votre inscription a bien été prise en compte.</p>
        </div>
        
    </body>

</html>