<?php session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php"); 
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

if (!(in_array('admin', $_SESSION['user_roles']) )) {
    header('Location: /pages/access_denied.php'); // Redirect to an access denied page
    exit;
}
?>
<!DOCTYPE html>


<html>
<head>
	<title>Inscription/Connexion</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	
</head>
<body>

<div id="inscription" class="tab-content">
		
		<form action="/php/register.inc.php" method="post">
		
			<div class="form-container">
			<h2>Inscription| GBABLÉ</h2>
				<label for="lastname"><b>Nom</b></label>
				<input type="text" placeholder="Entrez votre nom" name="lastname" required>

				<label for="firstname"><b>Prénom</b></label>
				<input type="text" placeholder="Entrez votre prénom" name="firstname" required>

				<label for="email"><b>Email</b></label>
				<input type="text" placeholder="Entrez votre email" name="email" required>

				<label for="pwd"><b>Mot de passe</b></label>
				<input type="password" placeholder="Entrez votre mot de passe" name="pwd" required>

				<label for="pwdConfirmed"><b>Confirmez votre mot de passe</b></label>
				<input type="password" placeholder="Confirmez votre mot de passe" name="pwdConfirmed" required>

				<button type="submit">S'inscrire</button>
			</div>
			<div class="tab-buttons">

			</div>
		</form>
	
	</div>

	<script src="/js/script.js"></script>
	
</body>

</html>
