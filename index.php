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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="public/css/headercss.css">
	<link rel="stylesheet" type="text/css" href="public/css/style.css">
</head>

<body>
	<?php
	if (isset($_POST['login']))  //Si on vient d'envoyer le login
	{
		//Vérification correspondance login / mdp
		$user = $bdd->prepare("SELECT * FROM account WHERE username=? ");
		$user->execute(array($_POST['login']));
		$currentuser = $user->fetch();
		if (password_verify($_POST['pass'], $currentuser['password'])) {
			$_SESSION['username'] = $_POST['login'];
			include("views/header.php");   //Affichage du header après l'enregistrement de session ( Affichage nom prenom)
			$user->closeCursor();
		} else {
			include("views/header.php");
			echo "<h2>Mot de passe ou Pseudonyme incorrect</h2>";
			$user->closeCursor();
		}
	} else {
		include("views/header.php");
	}

	if (!isset($_SESSION['username']))  //Si aucune session n'est enregistré
	{
		if (isset($_GET['recover']) && $_GET['recover'] == 1) {
			echo "<h3 class='passmodified'>Mot de passe modifié!</h3>";
		}
	?>
		<div class=box_login>
			<div class="form_login">
				<form action="index.php" method="post">
					<div class="name_log_champ">
						<label for="identifiant">Login</label><br><br>
						<label for="password">Password</label>
					</div>
					<div class="log_champ">
						<input type="text" name="login" id="identifiant"></br><br>
						<input type="password" name="pass" id="password"></br><br>
						<input type="submit" value="Connexion" class="login_button">
					</div>
			</div>
			<div class="recoverpass"><a href="recoverpass.php">Mot de passe oublié ?</a></div>
			<div class="inscription">
				<p>Pas encore de compte ?<a href="register.php">Inscription</a></p>
			</div>
		</div>
	<?php
	} else {  //Si la session existe on affiche le site
	?>
		<div class="presentation">
			<h1>Le GBAF et son Extranet</h1>
			<div class="text_presentation">
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
		</div>
		<div class="acteurs_partenaire">
			<h2>Acteurs et Partenaire du Groupe GBAF</h2><br>
			<?php
			//Recupération des acteurs GBAF
			$allactor = catchallactor($bdd);
			$numberactor = $bdd->query("SELECT COUNT(*) AS Nbactor FROM acteur");
			$number = $numberactor->fetch();
			//Préparation de la description raccourcie
			$descactor = $bdd->prepare("SELECT SUBSTRING(description,1,150) AS shortdesc FROM acteur WHERE nom=? ");
			for ($i = 0; $i < $number['Nbactor']; $i++) {
				$currentactor = $allactor->fetch();
				$descactor->execute(array($currentactor['nom']));
				$currentshortdesc = $descactor->fetch();
			?>
				<div class=actordisplay>
					<img src="public/img/<?php echo $currentactor['logo']  ?>" alt="actorthumbnails" class="logoactor">
					<div class="actordesc">
						<p> <?php echo $currentshortdesc['shortdesc'] . "..."; ?></p>
						<a href="pageacteur.php?id_acteur=<?php echo $currentactor['id_acteur']; ?>" class="actorpage">Lire la suite</a>
					</div>
				</div><?php
						$descactor->closeCursor();
					}
					$allactor->closeCursor();
					$numberactor->closeCursor();
						?>

		</div>

	<?php }
	include("views/footer.php") ?>
</body>

</html>