<?php
session_start();
if (isset($_POST['logout']) && $_POST['logout'] == "true") {
    session_destroy();
    session_start();
}

include("src/bdd/bddcall.php");
$bdd = bddcall();
$currentuser = log_user($bdd, $_SESSION['username']);
$modif = false;
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
    if (isset($_POST['username']) && $_POST['username'] != $_SESSION['username']) { //Verif des données modifiées une à une

    }
    if (isset($_POST['firstname']) &&  $_POST['firstname'] != $currentuser['nom']) {
        $champmodifié = "nom";
        modifnom($bdd, $_POST['firstname'], $currentuser['id_user']);
        $currentuser = log_user($bdd, $_SESSION['username']);
        $modif = true;
    }
    if (isset($_POST['secondname']) && $_POST['secondname'] != $currentuser['prenom']) {
        $champmodifié = "prenom";
        modifprenom($bdd, $_POST['secondname'], $currentuser['id_user']);
        $currentuser = log_user($bdd, $_SESSION['username']);
        $modif = true;
    }
    if (isset($_POST['pass']) && preg_match("#.{4,}#", $_POST['pass'])) {
        if ($_POST['pass'] == $_POST['conf_pass']) {
            $champmodifié = "password";
            modifpassword($bdd, password_hash($_POST['pass'], PASSWORD_DEFAULT), $currentuser['id_user']);
            $currentuser = log_user($bdd, $_SESSION['username']);
            $modif = true;
        } else {
            echo "<p style='text-align:center;'>Les mots de passe ne sont pas identiques</p>";
        }
    }
    include("views/header.php");

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
        <h2>Paramètres du compte</h2><?php if ($modif = true) echo "<br><p style='text-align:center;'>Informations modifiés</p>" ?>
        <form action="profilpage.php" method="POST" class="profil_form">
            <div class="profil_nom_champ">
                <label for="identifiant">Pseudonyme</label><br>
                <label for="new_password" style="margin-top:0.25rem;">Nouveau mot de passe</label><br>
                <label for="confirm_password">Confirmer le mot de passe</label><br>
                <label for="nom" style="margin-top: 0.4rem">Nom</label><br>
                <label for="prenom">Prénom</label><br>
            </div>
            <div class="profil_champ">
                <input type="text" name="username" id="identifiant" value="<?php echo $currentuser['username']; ?>"><br>
                <input type="password" name="pass" id="new_password"><br>
                <input type="password" name="conf_pass" id="confirm_password"><br>
                <input type="text" name="firstname" id="nom" value="<?php echo $currentuser['nom']; ?>"><br>
                <input type="text" name="secondname" id="prenom" value="<?php echo $currentuser['prenom']; ?>"><br>
                <input type="hidden" name="currentpass" value="<?php echo $_POST['currentpass']; ?>">
                <input type="submit" value="Modifier vos informations">
            </div>
        </form>
    <?php }
    include("views/footer.php") ?>
</body>

</html>