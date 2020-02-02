<?php
	session_start();
	if (!isset($_SESSION['login'])) {   //On vérifie qu'il n'y pas de session enregistré
		if (isset($_POST['login'])) {	//si l'utilisateur vient de se connecteur enregistré le nom de session
			$login=$_POST['login'];
			$_SESSION['login']=$login;
			setcookie('pseudo',$login, time() + 60,null,null,false,true);   //test du cookie en httpOnly
		}
	}
	if (isset($_POST['logout']) AND $_POST['logout']==1) {     //Test du bouton de déconnexion
		session_destroy();
		$_POST['logout']==0;
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Extranet Bancaire</title>
</head>
<body>
	
	<?php
	if (isset($_COOKIE['pseudo'])) {     //test cookie
		echo $_COOKIE['pseudo'];
	}
	
	if (isset($_POST['pass'])) {            //Mot de passe envoyer ?
		if ($_POST['pass']=='kangourou') {    //Mot de passe valide
			?>
			<h2>
				<?php

			 echo $_SESSION['login']; //Test du session login
			
			 ?> 	
				 Bienvenue sur l'extranet du groupe bancaire </h2>
		<?php		
		}
		else{  // Renvoie du formulaire de connexion si mdp incorrect
		?>
			<form action="home.php" method="post">
		<label for="identifiant">Login</label>
		<input type="text" name="login" id="identifiant" >
		<br>
		<label for="password">Password</label>
		<input type="password" name="pass" id="password">
		<input type="submit" value="Valider" >
			</form>
			<h2>Mot de passe non valide</h2>
			<p><?php echo $_POST['login']; ?> nous vous rappelons que ce site est une propriété du groupe bancaire <strong>#####</strong>, ne tenter pas d'usurper une identidé sous peine de poursuite<p>
	<?php
		}		
	}
	else{ //Si aucun mot de passe n'a été envoyé
	?>
		<form action="home.php" method="post">
		<label for="identifiant">Login</label>
		<input type="text" name="login" id="identifiant" >
		<br>
		<label for="password">Password</label>
		<input type="password" name="pass" id="password">
		<input type="submit" name="Valider">
		</form>
		<h2>Bienvenue sur l'extranet bancaire du Groupe ###### veuillez vous identifier</h2>
	<?php
	}
	?>
	<footer>
		<p style="text-align: center;">COPYRIGHT - Groupe Bancaire</p>
		<?php
			if (isset($_SESSION['login']) AND (!isset($_POST['logout']))) { //Si une session est enregistré bouton de déconnexion
		?>
		<form action="home.php" method="post">   
			<input type="hidden" name="logout" value="1">
			<input type="submit" name="Deconnexin" value="Deconnexion">
		</form>
		<?php
			}
		?>
	</footer>
</body>
</html>