<div class="header">
	<div class="divlogo_gbaf">
		<a href="index.php"><img src="./public/img/gbaf.png" class="logo_gbaf"></a>
	</div>
	<div class="user_spec">
		<?php if (isset($_SESSION['username'])) {
			$userinfo = log_user($bdd, $_SESSION['username']);
		?>
			<p><?php echo htmlspecialchars($userinfo['nom']) . " : " . htmlspecialchars($userinfo['prenom']) ?></p>
			<form method="post">
				<input type="hidden" name="logout" value="true">
				<input class="button_logout" type="submit" value="DECONNEXION">
			</form>
			<a href="profilpage.php">Paramètres de compte</a>
		<?php } ?>

	</div>
</div>