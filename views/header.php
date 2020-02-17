<div class="header">
	<div class="divlogo_gbaf">
		<img src="./public/img/gbaf.png" class="logo_gbaf">
	</div>
	<div class="user_spec">
		<?php if (isset($_SESSION['username'])) { ?>
			<p>First Name : Last Name </p>
			<form method="post">
				<input type="hidden" name="logout" value="true">
				<input type="submit" value="Déconnexion">
			</form>
		<?php } ?>
	</div>
</div>