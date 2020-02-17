<?php
session_start();
if (isset($_POST['logout']) && $_POST['logout'] == "true") {
	session_destroy();
	session_start();
}

include("src/bdd/bddcall.php");
$bdd = bddcall();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<title>Extranet GBAF</title>
	<link rel="stylesheet" type="text/css" href="public/css/headercss.css">
	<link rel="stylesheet" type="text/css" href="public/css/style.css">
</head>

<body>
	<?php include("views/header.php");
	if (isset($_POST['login']))  //Si on vient d'envoyer le login
	{
		echo "<h1>test</h1>";
		//Vérification correspondance login / mdp
		$user = $bdd->prepare("SELECT * FROM account WHERE username=? ");
		$user->execute(array($_POST['login']));
		$currentuser = $user->fetch();
		echo '<p>La question est:' . $currentuser['password'] . '</p>';
		if (password_verify($_POST['pass'], $currentuser['password'])) {
			$_SESSION['username'] = $_POST['login'];
		} else {
			echo "<h2>Mot de passe ou Pseudonyme incorrect</h2>";
		}
	}

	if (!isset($_SESSION['username']))  //Si aucune session n'est enregistré
	{

	?>
		<div class="form_login">
			<form action="index.php" method="post">
				<label for="identifiant">Login</label>
				<input type="text" name="login" id="identifiant"></br><br>
				<label for="password">Password</label>
				<input type="password" name="pass" id="password"></br>
				<input type="submit" value="Valider">
		</div>
		<div class="inscription">
			<p>Pas encore de compte ?<a href="register.php">Inscription</a></p>
		</div>

	<?php
	} else {  //Si la session existe on affiche le site
	?>
		<div class="presentation">
			<h1>Le GBAF et son Extranet</h1>
			<p>
				Le Groupement Banque Assurance Français (GBAF) est une fédération
				représentant les 6 grands groupes français :
			</p>
			<ul>
				<li>BNP Paribas</li>
				<li>BPCE</li>
				<li>Crédit Agricole</li>
				<li>Crédit Mutuel-CIC</li>
				<li>Société Générale</li>
				<li>La Banque Postale</li>
			</ul>
			<p>Ce site est destiné à regrouper des informations sur les différents acteurs et partenaires du secteur bancaire,
				tels que les associations ou les financeurs solidaires.
			</p>
		</div>
		<div class="acteurs_partenaire">

		</div>

	<?php }
	include("views/footer.php") ?>
</body>

</html>