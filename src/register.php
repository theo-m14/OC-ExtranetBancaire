<?php
session_start();
include("bdd/bddcall.php");
$bdd = bddcall();
$enregistrement_utilisateur = registeruser($bdd);

$verif_username = $bdd->query('SELECT username FROM account');
$pseudo_dispo = true;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" type="text/css" href="../public/css/headercss.css">
    <link rel="stylesheet" type="text/css" href="../public/css/styleregister.css">
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
</head>

<body>
    <?php include('../views/header.php');
    if (isset($_POST['register_login'])) {
        if ($_POST['pass'] == $_POST['conf_pass']) {  //Verif si les deux pass sont identiques
            while ($username = $verif_username->fetch()) {
                if ($username == $_POST['register_login']) {
                    $pseudo_dispo = false;  //Verif du pseudo disponible
                }
            }
            if ($pseudo_dispo) {
                $enregistrement_utilisateur->execute(array(
                    'nom' => $_POST['firstname'],
                    'prenom' => $_POST['secondname'],
                    'username' => $_POST['register_login'],
                    'password' => password_hash($_POST['pass'], PASSWORD_DEFAULT), //hacher le mdp
                    'question' => $_POST['secur_question'],
                    'reponse' => $_POST['secur_response']
                ));
                header('Location: http://localhost/oc-extranetbancaire/');
                exit();
            }
        }
    }
    ?>

    <h2>Inscription à l'extranet GBAF</h2>
    <form action="register.php" method="POST">
        <label for="register_identifiant">Pseudonyme</label>
        <input type="text" name="register_login" id="register_identifiant"></br><br>
        <label for="password">Mot de passe</label>
        <input type="password" name="pass" id="password"></br>
        <label for="confirm_password">Confirmer le mot de passe</label>
        <input type="password" name="conf_pass" id="confirm_password"></br>
        <label for="nom">Nom</label>
        <input type="text" name="firstname" id="nom"><br>
        <label for="prenom">Prénom</label>
        <input type="text" name="secondname" id="prenom"><br>
        <label for="question">Question de sécurité</label>
        <SELECT name="secur_question" id="question">
            <OPTION>Nom de jeune fille de votre mère
            <OPTION>Nom de votre premier animal de compagnie
            <OPTION>Votre film préféré
        </SELECT><br>
        <label for="reponse">Votre Réponse</label>
        <input type="text" name="secur_response" id="reponse"><br>
        <input type="submit" value="Valider">
    </form>
    <p>Déja un compte ? <a href="index.php">Connexion</a> </p> <?php include("../views/footer.php"); ?>
</body>

</html>