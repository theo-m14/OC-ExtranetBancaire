<?php
session_start();
include("src/bdd/bddcall.php");
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
    <link rel="stylesheet" type="text/css" href="public/css/headercss.css">
    <link rel="stylesheet" type="text/css" href="public/css/styleregister.css">
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
</head>

<body>
    <?php include('views/header.php'); ?>
    <h2>Inscription à l'extranet GBAF</h2>
    <?php
    if (isset($_POST['register_login'])) {
        if ($_POST['pass'] == $_POST['conf_pass'] && preg_match("#.{4,}#", $_POST['pass'])) {  //Verif si les deux pass sont identiques
            if (preg_match("#^[a-z0-9]{2,40}$#", $_POST['register_login'])) {
                if (preg_match("#^[a-z]{3,40}$#", $_POST['firstname']) && preg_match("#^[a-z]{3,40}$#", $_POST['secondname'])) {
                    if (preg_match("#^[a-z0-9]{3,40}$#", $_POST['secur_response'])) {
                        while ($username = $verif_username->fetch()) {
                            if ($username['username'] == $_POST['register_login']) {
                                echo "<p class='info_form'>Pseudo indisponible</p>"; //Verif du pseudo disponible
                                $pseudo_dispo = false;
                            }
                        }
                        if ($pseudo_dispo) {
                            $enregistrement_utilisateur->execute(array(
                                'nom' => $_POST['firstname'],
                                'prenom' => $_POST['secondname'],
                                'username' => $_POST['register_login'],
                                'password' => password_hash($_POST['pass'], PASSWORD_DEFAULT), //hacher le mdp
                                'question' => $_POST['secur_question'],
                                'reponse' => password_hash($_POST['secur_response'], PASSWORD_DEFAULT),
                            ));
                            header('Location: http://localhost/oc-extranetbancaire/');
                            exit();
                        }
                    } else {
                        echo "<p class='info_form'>Réponse de sécurité non conforme. Elle doit être composé d'au moins 3 caractères alphanumériques seulement</p>";
                    }
                } else {
                    echo "<p class='info_form'>Nom ou prénom non conforme</p>";
                }
            } else {
                echo "<p class='info_form'>Pseudo non conformes. Il doit contenir seulement des caractères alphanumériques(Au moins 2)</p>";
            }
        } else {
            if (preg_match("#.{4,}#", $_POST['pass'])) {
                echo "<p class='info_form'>Les mots de passes saisis sont différents";
            } else echo "<p class='info_form'>Votre mot de passe doit contenir au moins 4 caractères";
        }
    }
    ?>

    <form action="register.php" method="POST" class="register_form">
        <div class="nom_champ">
            <label for="register_identifiant">Pseudonyme</label><br>
            <label for="password" style="margin-top:0.25rem;">Mot de passe</label><br>
            <label for="confirm_password">Confirmer le mot de passe</label><br><br>
            <label for="nom">Nom</label><br>
            <label for="prenom">Prénom</label><br>
            <label for="question">Question de sécurité</label><br>
            <label for="reponse">Votre Réponse</label>
        </div>
        <div class="champ">
            <input type="text" name="register_login" id="register_identifiant"><br>
            <input type="password" name="pass" id="password"><br>
            <input type="password" name="conf_pass" id="confirm_password"><br>
            <input type="text" name="firstname" id="nom"><br>
            <input type="text" name="secondname" id="prenom"><br>
            <SELECT name="secur_question" id="question"><br>
                <OPTION>Nom de jeune fille de votre mère
                <OPTION>Nom de votre premier animal de compagnie
                <OPTION>Votre film préféré
            </SELECT><br>
            <input type="text" name="secur_response" id="reponse"><br>

            <input type="submit" value="Inscription">
        </div>
    </form>
    <p>Déja un compte ? <a href="index.php">Connexion</a> </p> <?php include("views/footer.php"); ?>
</body>

</html>