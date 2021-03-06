<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="public/css/headercss.css">
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
</head>

<body>
    <?php
    include("views/header.php");
    include("src/bdd/bddcall.php");
    $bdd = bddcall();
    if (isset($_POST['pseudonyme'])) {
        $user = log_user($bdd, $_POST['pseudonyme']);
        if ($_POST['secur_question'] == $user['question'] && password_verify($_POST['secur_response'], $user['reponse'])) {
            if (preg_match("#.{4,}#", $_POST['new_pass'])) {
                $passchange = $bdd->prepare("UPDATE account SET password = ? WHERE id_user = ?");
                $passchange->execute(array(password_hash($_POST['new_pass'], PASSWORD_DEFAULT), $user['id_user']));
                header('Location: http://localhost/oc-extranetbancaire/?recover=1');
                exit();
            } else {
                echo "<p class='info_form'>Votre mot de passe doit contenir au moins 4 caractères</p>";
            }
        } else {
            echo "<p class='info_form'>Informations incorrects</p>";
        }
    }
    ?>
    <form class="form_recoverpass" action="recoverpass.php" method="POST">
        <div class="nomchamp_recoverpass">
            <br><label for="pseudo">Pseudonyme</label><br>
            <label for="question">Question de sécurité</label><br>
            <label for="reponse">Votre Réponse</label><br>
            <label for="newpass">Nouveau mot de passe<label><br>
        </div>
        <div class="champ_recoverpass">
            <input type="text" name="pseudonyme" id="pseudo"><br>
            <SELECT name="secur_question" id="question">
                <OPTION>Nom de jeune fille de votre mère
                <OPTION>Nom de votre premier animal de compagnie
                <OPTION>Votre film préféré
            </SELECT><br>
            <input type="text" name="secur_response" id="reponse"><br>
            <input type="password" name="new_pass" id="newpass"><br>
            <input type="submit" value="Valider">
        </div>
    </form>
    <p style="text-align: center"><a href="index.php">Retour à la connexion</a> </p>
    <?php include("views/footer.php") ?>
</body>

</html>