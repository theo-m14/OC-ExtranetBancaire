<?php
session_start();
if (isset($_POST['logout']) && $_POST['logout'] == "true") {
    session_destroy();
    session_start();
}

include("src/bdd/bddcall.php");
$bdd = bddcall();
if (isset($_SESSION['username'])) { //Vérification de l'utilisateur connecté
    $currentuser = log_user($bdd, $_SESSION['username']);
} else {
    header('Location: http://localhost/oc-extranetbancaire/'); //renvoi sur index.php -> login
    exit();
}
$modif = false;
$pseudodispo = true;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votre Profil</title>
    <link rel="stylesheet" type="text/css" href="public/css/headercss.css">
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
</head>

<body>
    <?php
    $carac_alphanumérique = "#^[a-z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]{2,40}$#i";
    if (isset($_POST['username']) && $_POST['username'] != $_SESSION['username']) {
        if (preg_match($carac_alphanumérique, $_POST['username'])) { //Verif des données modifiées une à une
            $verifusername = $bdd->query("SELECT username FROM account");
            while ($username = $verifusername->fetch()) {
                if ($_POST['username'] == $username['username']) {
                    $pseudodispo = false;
                }
            }
            if ($pseudodispo) {
                $champmodifié = "username";
                modifusername($bdd, $_POST['username'], $currentuser['id_user']);
                $_SESSION['username'] = $_POST['username'];
                $currentuser = log_user($bdd, $_SESSION['username']);
                $modif = true;
            }
        } else {
            $erromessage = "<p class='info_form'>Pseudo non conformes. Il doit contenir seulement des caractères alphanumériques(Au moins 2)</p>";
        }
    }
    if (isset($_POST['firstname']) &&  $_POST['firstname'] != $currentuser['nom']) {
        if (preg_match($carac_alphanumérique, $_POST['firstname'])) {
            $champmodifié = "nom";
            modifnom($bdd, $_POST['firstname'], $currentuser['id_user']);
            $currentuser = log_user($bdd, $_SESSION['username']);
            $modif = true;
        } else {
            $erromessage = "<p class='info_form'>Prénom non conforme</p>";
        }
    }
    if (isset($_POST['secondname']) && $_POST['secondname'] != $currentuser['prenom']) {
        if (preg_match($carac_alphanumérique, $_POST['secondname'])) {
            $champmodifié = "prenom";
            modifprenom($bdd, $_POST['secondname'], $currentuser['id_user']);
            $currentuser = log_user($bdd, $_SESSION['username']);
            $modif = true;
        } else {
            $erromessage = "<p class='info_form'>Nom non conforme</p>";
        }
    }
    if (isset($_POST['pass']) && preg_match("#.{4,}#", $_POST['pass'])) {
        if ($_POST['pass'] == $_POST['conf_pass']) {
            $champmodifié = "password";
            modifpassword($bdd, password_hash($_POST['pass'], PASSWORD_DEFAULT), $currentuser['id_user']);
            $currentuser = log_user($bdd, $_SESSION['username']);
            $modif = true;
        } else {
            include("views/header.php");
            echo "<p style='text-align:center;'>Les mots de passe ne sont pas identiques</p>";
        }
    } else {
        include("views/header.php");
    }

    if ((!isset($_POST['currentpass']) || !password_verify($_POST['currentpass'], $currentuser['password'])) && !$modif) { //Demande de mdp avant accccès aux informations
        if (isset($_POST['currentpass']) && !password_verify($_POST['currentpass'], $currentuser['password'])) {
            echo '<h2>Mot de passe incorrect</h2>';
        } ?>
        <div class="verifpass">
            <form action="profilpage.php" method="POST">
                <label for="currentpass">Taper votre mot de passe pour accéder à vos informations</label><br>
                <input type="password" name="currentpass" id="currentpass" autofocus>
                <input type="submit" value="Valider">
            </form>
        </div>
    <?php } else {
    ?>
        <h2>Paramètres du compte</h2><?php if ($modif) {
                                            echo "<br><p class=info_form>Informations modifiés</p>";
                                        }
                                        if (!$pseudodispo) {
                                            echo "<br><p class=info_form>Pseudo indisponible</p>";
                                        }
                                        if (isset($erromessage)) {
                                            echo $erromessage;
                                        } ?>
        <form action="profilpage.php" method="POST" class="profil_form">
            <div class="profil_nom_champ">
                <label for="identifiant">Pseudonyme</label><br>
                <label for="new_password" style="margin-top:0.25rem;">Nouveau mot de passe</label><br>
                <label for="confirm_password">Confirmer le mot de passe</label><br>
                <label for="prenom" style="margin-top: 0.4rem">Prénom</label><br>
                <label for="nom">Nom</label><br>
            </div>
            <div class="profil_champ">
                <input type="text" name="username" id="identifiant" value="<?php echo htmlspecialchars($currentuser['username']); ?>"><br>
                <input type="password" name="pass" id="new_password"><br>
                <input type="password" name="conf_pass" id="confirm_password"><br>
                <input type="text" name="firstname" id="nom" value="<?php echo htmlspecialchars($currentuser['nom']); ?>"><br>
                <input type="text" name="secondname" id="prenom" value="<?php echo htmlspecialchars($currentuser['prenom']); ?>"><br>
                <input type="hidden" name="currentpass" value="<?php echo $_POST['currentpass']; ?>">
                <input type="submit" value="Modifier vos informations">
            </div>
        </form>
    <?php }
    include("views/footer.php") ?>
</body>

</html>