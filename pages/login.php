<?php include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Connexion | GBABLÉ</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	
</head>
<body>
<div class="tab-content-wrapper">
	<div id="connexion" class="tab-content " >
		
		<form action="/php/login.inc.php" method="post">
			<div class="form-container">
			<h2>Connexion</h2>
				<label for="email"><b>Email</b></label>
				<input type="text" placeholder="Entrez votre email" name="email" required>

				<label for="pwd"><b>Mot de passe</b></label>
				<input type="password" placeholder="Entrez votre mot de passe" name="pwd" required>

				<button type="submit">Se connecter</button>
				<label>
					<input type="checkbox" checked="checked" name="remember"> Se souvenir de moi
				</label>
			</div>
			
		</form>
		
	</div>
</div>
	<script src="/js/script.js"></script>
	
</body>

</html>
