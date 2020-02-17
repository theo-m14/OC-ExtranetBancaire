<?php
session_start();
try {           //Récupération BDD
    $bdd = new PDO('mysql:host=localhost;dbname=extranet_gbaf;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
$enregistrement_utilisateur = $bdd->prepare('INSERT INTO users(nom,prenom,username,password,question,reponse)
                                VALUES(:nom, :prenom, :username, :password, :question, :reponse)'); //préparation d'enregistrement

$verif_username = $bdd->query('SELECT username FROM users');
$pseudo_dispo = true;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" type="text/css" href="css/headercss.css">
    <link rel="stylesheet" type="text/css" href="css/styleregister.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <?php include('header.php');
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
    <p>Déja un compte ? <a href="index.php">Connexion</a> </p> <?php include('footer.php'); ?>
</body>

</html>