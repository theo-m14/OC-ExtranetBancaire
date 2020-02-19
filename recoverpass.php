<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
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
            $passchange = $bdd->prepare("UPDATE account SET password = ? WHERE id_user = ?");
            $passchange->execute(array(password_hash($_POST['new_pass'], PASSWORD_DEFAULT), $user['id_user']));
            header('Location: http://localhost/oc-extranetbancaire/?recover=1');
            exit();
        } else {
            echo "<h3 class='passmodified'>Informations incorrects</h3>";
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

</body>

</html>