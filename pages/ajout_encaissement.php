<?php
session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
	header('Location: /pages/login.php');
	exit;
}

// Check if the user has the correct role to access this page

if (!(in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles'])|| in_array('booking', $_SESSION['user_roles']))) {
	header('Location: /pages/access_denied.php'); // Redirect to an access denied page
	exit;
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Ajouter un encaissement | GBABLÉ</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
		integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
	<?php include('/home2/babimors/gbable.motorsfeere.com/pages/header.php') ?>

	<div class="container mt-5">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<div class="card">
					<div class="card-header  text-center mb-4">
						<h1>Nouveau Paiement</h1>
					</div>
					<div class="card-body">
						<form action="/php/generate.php" method="post" class="form-container">
							<div class="form-group">
								<label for="date_paiement"><b>Date de paiement</b></label>
								<input type="date" placeholder="Entrez la date de paiement" name="date_paiement"
									class="form-control" required>
							</div>

							<div class="form-group">
								<label for="id_invoice"><b>Numéro de facture</b></label>
								<select name="id_invoice" id="numero_facture" class="form-control" required>
									<option value="">Sélectionnez le numéro de facture</option>
									<?php
										$sql = "SELECT * FROM `invoice` WHERE remaining_balance >= 0 AND status IN ('Pending', 'Confirmed') ORDER BY `invoice`.`date_insertion` DESC";
										$stmt = $connexion->prepare($sql);
									 $stmt->execute();
									while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										echo '<option value="' . htmlspecialchars($row['id']) . '">'. htmlspecialchars($row['nom_facture']) .'- Solde Restant :'.number_format(htmlspecialchars($row['remaining_balance']), 0, '', ' '). ' Fcfa</option>';
									}
									?>
								</select>
							</div>

							<div class="form-group">
								<label for="montant_paye"><b>Montant payé</b></label>
								<input type="number" placeholder="Entrez le montant payé" name="montant_paye"
									class="form-control" inputmode="numeric" required>
							</div>

							<button type="submit" name="newIncome" class="btn btn-primary">Ajouter le paiement</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
		integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
		integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
		integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
		crossorigin="anonymous"></script>

</body>

</html>