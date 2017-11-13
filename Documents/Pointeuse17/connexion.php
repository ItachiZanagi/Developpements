<?php
session_start();
/* Indique le bon format des entêtes (par défaut apache risque de les envoyer au standard ISO-8859-1)*/
header('Content-type: text/html; charset=UTF-8');

/* Initialisation de la variable du message de réponse*/
$message = null;

/* Récupération des variables issues du formulaire par la méthode post*/
$pseudo = filter_input(INPUT_POST, 'pseudo');
$pass = filter_input(INPUT_POST, 'pass');

/* Si le formulaire est envoyé*/
if (isset($pseudo,$pass)) 
{
    
    /* Teste que les valeurs ne sont pas vides ou composées uniquement d'espaces */  
    $pseudo = trim($pseudo) != '' ? $pseudo : null;
    $pass = trim($pass) != '' ? $pass : null;
  
  
  /* Si $pseudo et $pass différents de null */
  if(isset($pseudo,$pass)) 
  {
    $hostname = "localhost";
    $database = "test";
    $username = "root";
    $password = "root";

    
    /* Configuration des options de connexion */
    
    /* Désactive l'éumlateur de requêtes préparées (hautement recommandé) */
    $pdo_options[PDO::ATTR_EMULATE_PREPARES] = false;
    
    /* Active le mode exception */
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    
    /* Indique le charset */
    $pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
    
    /* Connexion */
    try
    {
      $connect = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password, $pdo_options);
    }
    catch (PDOException $e)
    {
      exit('problème de connexion à la base');
    }    
    
    /* Requête pour récupérer les enregistrements répondant à la clause : champ du pseudo et champ du mdp de la table = pseudo et mdp posté dans le formulaire */
    $requete = "SELECT * FROM C1IDE WHERE pseudo = :pseudo AND pass = :password";  
    
    try
    {
      /* Préparation de la requête*/
      $req_prep = $connect->prepare($requete);
      
      /* Exécution de la requête en passant les marqueurs et leur variables associées dans un tableau*/
      $req_prep->execute(array(':pseudo'=>$pseudo,':password'=>$pass));
      
      /* Création du tableau du résultat avec fetchAll qui récupère tout le tableau en une seule fois*/
      $resultat = $req_prep->fetchAll(); 
      $nb_result = count($resultat);
      
      if ($nb_result == 1)
      {
        /* Démarre une session si aucune n'est déjà existante et enregistre le pseudo dans la variable de session $_SESSION['login'] qui donne au visiteur la possibilité de se connecter.  */
        if (!session_id()) {
            session_start();
        }
        
        // Récupération des données qui nous intéresse
        $_SESSION['id'] = $resultat[0]['id'];
        $_SESSION['login'] = $pseudo;
        $_SESSION['nom'] = $resultat[0]['nom'];
        $_SESSION['pass'] = $resultat[0]['pass'];
        $_SESSION['prenom'] = $resultat[0]['prenom'];
        $_SESSION['level'] = $resultat[0]['level'];
        
        $message = 'Bonjour '.htmlspecialchars($_SESSION['prenom']). ' ' .htmlspecialchars($_SESSION['nom']). ', vous serez redirigé dans 1 secondes';
        
        // Affichage selon le profil de l'utilisateur
        if($_SESSION['level'] === 1) {
            header( "refresh:1;url=inc/dashboard/levels/direction/index_dir.php" );
        } else if ($_SESSION['level'] === 2) {
            header( "refresh:1;url=inc/dashboard/levels/ass_mat/index_ass.php" );
        } else if ($_SESSION['level'] === 3) {
            header( "refresh:1;url=inc/dashboard/levels/parents/index_par.php" );
        } else if ($_SESSION['level'] === 0) {
            header( "refresh:1;url=inc/dashboard/levels/root/index_root.php" );
        }
        //exit();
        
      }
      else if ($nb_result > 1)
      {
        /* Par sécurité si plusieurs réponses de la requête mais si la table est bien construite on ne devrait jamais rentrer dans cette condition */
        $message = 'Problème de d\'unicité dans la table';
      }
      else
      {   /* Le pseudo ou le mot de passe sont incorrect */
        $message = 'Le pseudo ou le mot de passe sont incorrect';
      }
    }
    catch (PDOException $e)
    {
      $message = 'Problème dans la requête de sélection';
    }	
  }
  else 
  {/*au moins un des deux champs "pseudo" ou "mot de passe" n'a pas été rempli*/
    $message = 'Les champs Pseudo et Mot de passe doivent être remplis.';
  }
}
?>

<!doctype html>
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Formulaire de connexion</title>
<!--[if IE]>
<style type="text/css">
   body {background-color: #cccccc !important;}
/style>
<![endif]-->
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="bootstrap/css/datepicker3.css" rel="stylesheet">
	<link href="bootsrap/css/styles.css" rel="stylesheet">
        <link href="bootstrap/css/navbar.css" rel="stylesheet">
        <link href="css/connexion_style.css" rel="stylesheet">
        <link rel="stylesheet" href="bootstrap/css/font-awesome.min.css">

</head>

<body>
    <?php require 'inc/header.php' ?>

    	<div class="row">
            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">Connexion</div>
                    <div class="panel-body">
                        <form role="form" action = "#" method="post">
                            <fieldset>
                                
                                <div class="form-group">
                                    <input class="form-control" type="text" name="pseudo" id="pseudo" autofocus="">
                                </div>
                                
                                <div class="form-group">
                                    <input class="form-control" type="password" name="pass" id="pass" value="">
                                </div>
                                
                                <!--
                                <div>
                                    <label>
                                        <a href="inscription.php"> S'inscrire</a>
                                    </label>
                                </div>
                                -->
                                
                                <div>
                                    <label>
                                        <a href="#"> Mot de passe oublié</a>
                                    </label>
                                </div>
                                
                                <p><input type="submit" value="Envoyer" id = "valider" class="btn btn-primary"/></p>
                                
                            </fieldset>
                        </form>
                        <p id = "messageErreur"><?= $message?:'' ?></p>
                    </div>
                </div>
            </div><!-- /.col-->
	</div><!-- /.row -->	
	
<script src="bootstrap/js/jquery-1.11.1.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
        
</body>
</html>