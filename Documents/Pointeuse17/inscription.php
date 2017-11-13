<?php
/* Indique le bon format des entêtes (par défaut apache risque de les envoyer au standard ISO-8859-1)*/
header('Content-type: text/html; charset=UTF-8');

/* Initialisation de la variable du message de réponse*/
$message = null;

/* Récupération des variables issues du formulaire par la méthode post*/
$pseudo = filter_input(INPUT_POST, 'pseudo');
$pass = filter_input(INPUT_POST, 'pass');
$nom = filter_input(INPUT_POST, 'nom');
$prenom = filter_input(INPUT_POST, 'prenom');
$age = filter_input(INPUT_POST, 'age');
$adresse = filter_input(INPUT_POST, 'adresse');
$ville = filter_input(INPUT_POST, 'ville');
$cp = filter_input(INPUT_POST, 'cp');
$mail = filter_input(INPUT_POST, '$mail');
$level = filter_input(INPUT_POST, 'level');


/* Si le formulaire est envoyé */
if (isset($pseudo,$pass,$nom,$prenom,$age,$adresse,$cp,$mail,$level)) 
{   

    /* Teste que les valeurs ne sont pas vides ou composées uniquement d'espaces  */ 
    $pseudo = trim($pseudo) != '' ? $pseudo : null;
    $pass = trim($pass) != '' ? $pass : null;
    $nom = trim($nom) != '' ? $nom : null;
    $prenom = trim($prenom) != '' ? $prenom : null;
    $age = trim($age) != '' ? $age : null;
    $adresse = trim($adresse) != '' ? $adresse : null;
    $ville = trim($ville) != '' ? $ville : null;
    $cp = trim($cp) != '' ? $cp : null;
    $mail = trim($mail) != '' ? $mail : null;
    $level = trim($level) != '' ? $level : null;
   

    /* Si [$var] différents de null */
    if(isset($pseudo,$pass,$nom,$prenom,$age,$adresse,$cp,$mail,$level)) 
    {
    $hostname = "localhost";
    $database = "test";
    $username = "root";
    $password = "root";
    
    /* Configuration des options de connexion */
    
    /* Désactive l'éumlateur de requêtes préparées (hautement recommandé)  */
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
        
    
    /* Requête pour compter le nombre d'enregistrements répondant à la clause : champ du pseudo de la table = pseudo posté dans le formulaire */
    $requete = "SELECT count(*) FROM C1IDE WHERE pseudo = ? AND nom = ?";
    
    try
    {
      /* préparation de la requête*/
      $req_prep = $connect->prepare($requete);
      
      
      /* Exécution de la requête en passant la position du marqueur et sa variable associée dans un tableau*/
      $req_prep->execute(array(0=>$pseudo, 2=>$nom));
  
      /* Récupération du résultat */
      $resultat = $req_prep->fetchColumn();
      
      if ($resultat == 0) 
      /* Résultat du comptage = 0 pour ce pseudo, on peut donc l'enregistrer */
      {
        /* On effectue nos controles sur les champs*/  
          /* Controle sur l'age */
            if(is_numeric($age)) {
                if($age < 150) {
                    if(is_numeric($cp)) {

            /* Pour enregistrer la date actuelle (date/heure/minutes/secondes) on peut utiliser directement la fonction mysql : NOW()*/
            $insertion = "INSERT INTO C1IDE(pseudo,pass,nom,prenom,age,adresse,ville,cp,mail,level,date_enregistrement) VALUES(:pseudo, :password, :nom, :prenom, :age, :adresse, :ville, :cp, :mail, :level, NOW())";

            /* préparation de l'insertion */
            $insert_prep = $connect->prepare($insertion);

            /* Exécution de la requête en passant les marqueurs et leur variables associées dans un tableau*/
            $inser_exec = $insert_prep->execute(array(':pseudo'=>$pseudo,':password'=>$pass,':nom'=>$nom, ':prenom'=>$prenom, ':age'=>$age, ':adresse'=>$adresse, ':ville'=>$ville, ':cp'=>$cp, ':mail'=>$mail, 'level'=>$level));
var_dump($inser_exec);
            /* Si l'insertion s'est faite correctement...*/
                    if ($inser_exec === true) 
                    {
                      /* Démarre une session si aucune n'est déjà existante et enregistre le pseudo dans la variable de session $_SESSION['login'] qui donne au visiteur la possibilité de se connecter.  */
                      if (!session_id()) session_start();
                      $_SESSION['login'] = $pseudo;

                      /* A MODIFIER Remplacer le '#' par l'adresse de la page de destination, sinon ce lien indique la page actuelle.*/
                      $message = 'Votre inscription est enregistrée, redirection dans 1 secondes.';
                      header( "refresh:1;url=connexion.php" );
                      /*si redirection vers une page en cas de succès */
                      /*header("Location: menu.php");
                        exit();  */
                    }
                    } else {
                        $message = 'Le code postal est du type 69001, des entiers uniquement';
                    }
                } else {
                    $message = 'L\'age limite a été dépassé, veuillez nous contacter si il n\'y a pas d\'erreur';
                }
            } else {
                $message = 'L\'age doit etre un entier, exemple si j\'ai 30 ans: 30';
            }
      }
      else
      {   /* Le pseudo est déjà utilisé */
        $message = 'Les pseudo et/ou le nom est/sont déjà utilisé(s), veuillez le(s) changer.';
      }
    }
    catch (PDOException $e)
    {
      $message = 'Problème dans la requête d\'insertion';
    }	
  }
  else 
  {    /* un des champs n'a pas été rempli*/
    $message = 'Tous les champs doivent être remplis.';
  }
}
?>
<!doctype html>
<html lang="fr">
    <head>
        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Formulaire d'inscription - MedYa</title>
        <link rel="stylesheet" href="css/inscription_style.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.7.3/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        
    </head>

    <body>
        
    <div id = "inscription">
        <form action = "#" method = "post">
        <fieldset>Inscription</fieldset>
        <p><label for = "pseudo">Pseudo : </label><input type = "text" name = "pseudo" id = "pseudo" /></p>
        <p><label for = "pass">Mot de passe : </label><input type = "password" name = "pass" id = "pass" /></p>
        <p><label for = "nom">Nom : </label><input type = "text" name = "nom" id = "nom" /></p>
        <p><label for = "prenom">Prenom : </label><input type = "text" name = "prenom" id = "prenom" /></p>
        <p><label for = "age">Age : </label><input type = "text" name = "age" id = "age" /></p>
        <p><label for = "adresse">Adresse : </label><input name="adresse" id="adresse" type="text" placeholder="Adresse"></p>
        <p><label for = "cp">Code Postal : </label><input name="cp" id="cp" type="text" placeholder="CP"></p>
        <p><label for = "ville">Ville : </label><input name="ville" id="ville" type="text" placeholder="Ville"></p>
        <p><label for = "mail"> Adresse mail : </label><input name="mail" id="mail" type="text" placeholder="Email"></p>
 
        <p><label for = "level">Utilisateur :</label>
            <select name="level">
                <option value="1">Direction</option> 
                <option value="2" selected>Assistante Maternelle</option>
                <option value="3">Parent</option>
            </select>
        </p>

        <p><input type = "submit" value = "Envoyer" id = "valider" /></p>
        </form>
        <p id = "message"><?= $message?:'' ?></p>
    </div>
        
        <script src="js/autocompletion.js"></script>
        
    </body>

</html>