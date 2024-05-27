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
$allowed_roles = array('admin', 'booking'); // Define the allowed roles for this page
if (!(in_array('admin', $_SESSION['user_roles']) || in_array('booking', $_SESSION['user_roles'])|| in_array('marketing', $_SESSION['user_roles']))) {
    header('Location: /pages/access_denied.php'); // Redirect to an access denied page
    exit;
}

/*if (isset($_POST['apartment_name'])) {
    $apartment_name = $_POST['apartment_name'];
    $night_price = get_night_price_by_name($connexion, $apartment_name);
    echo $night_price;
} else {
    echo "Error: apartment_name not set";
}*/
?>
<!DOCTYPE html>
<html>

<head>
	<title>Nouvelle facture | GBABLÉ</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
		integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
	<?php include('/home2/babimors/gbable.motorsfeere.com/pages/header.php') ?>

	<div class="container mt-5">
		<div class="card">
			<div class="card-header">
				<h1>Nouvelle facture: Réservation</h1>
			</div>
			<div class="card-body">
				<form action="/php/generate.php" method="post" enctype="multipart/form-data">
					<div class="row mb-3">
						<div class="col-12">
							<div class="form-group">
								<label for="appartement">
									<h4>Appartement :</h4>
								</label>
								<select name="appartement" id="selectedAppartment" class="form-control" required>
									<option value="" disabled selected>Choisir un appartement</option>
									<?php
									$property_names = get_property_names($connexion);
									foreach ($property_names as $property_name) {
										echo "<option value=\"$property_name\">$property_name</option>";
									}
									?>
								</select>
							</div>
						</div>
					</div>
					<h4>Information client :</h4>
					<hr>
					<div class="row mb-3">
						<div class="col-md-6">
							<div class="form-group">
								<label for="nom"><b>Nom</b></label>
								<input class="form-control" type="text" placeholder="Entrez le nom" name="nom" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="prenom"><b>Prénom</b></label>
								<input class="form-control" type="text" placeholder="Entrez le prénom" name="prenom"
									required>
							</div>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-6">
							<div class="form-group">
								<label for="telephone"><b>Numéro de téléphone</b></label>
								<input class="form-control" type="text" placeholder="Entrez le numéro de téléphone"
									name="telephone" inputmode="numeric" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="email"><b>Adresse email</b></label>
								<input class="form-control" type="email" placeholder="Entrez l'adresse email"
									name="email" required>
							</div>
						</div>
					</div>
					<h4>Détails séjours :</h4>
					<hr>
					<div class="row mb-3">
						<div class="col-md-6">
							<div class="form-group">
								<label for="date_entree"><b>Date d'entrée</b></label>
								<input class="form-control" class="date" type="date"
									placeholder="Entrez la date d'entrée" name="date_entree" id="date_entree" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="date_sortie"><b>Date de sortie</b></label>
								<input class="form-control" class="date" type="date"
									placeholder="Entrez la date de sortie" name="date_sortie" id="date_sortie" required>
							</div>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-6">
							<div class="form-group">
								<label for="tarif_nuitee"><b>Tarif de la nuitée</b></label>
				
								<input class="form-control" type="number" placeholder="Entrez le tarif de la nuitée"
									name="tarif_nuitee" id="tarif_nuitee" inputmode="numeric" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="nombre_nuits"><b>Nombre de nuits</b></label>
								<input class="form-control" type="number"
									placeholder="Le nombre de nuits sera calculé automatiquement" name="nombre_nuits"
									id="nombre_nuits" readonly required>
							</div>
						</div>
					</div>
					<div class="col-md-6">
    <div class="form-group">
        <label for="discount"><b>Discount</b></label>
        <input class="form-control" type="number" placeholder="Enter discount" name="discount" id="discount" value="0" min="0" inputmode="numeric" required>
    </div>
</div>

					<div class="row mb-3">
						<div class="col-md-6">
							<div class="form-group">
								<label for="total"><b>Montant total</b></label>
								<input class="form-control" type="number"
									placeholder="Le montant total sera calculé automatiquement" name="total" id="total"
									readonly required>
							</div>
						</div>
					</div>
					<button class="btn btn-primary" name="newFct" type="submit">Envoyer la facture</button>
				</form>
			</div>
		</div>
	</div>
	<script>
document.getElementById('date_entree').addEventListener('change', calculateTotal);
document.getElementById('date_sortie').addEventListener('change', calculateTotal);
document.getElementById('tarif_nuitee').addEventListener('change', calculateTotal);
document.getElementById('discount').addEventListener('change', calculateTotal);

function calculateTotal() {
    const startDate = new Date(document.getElementById('date_entree').value);
    const endDate = new Date(document.getElementById('date_sortie').value);
    const tarifNuitee = parseFloat(document.getElementById('tarif_nuitee').value);
    const discount = parseFloat(document.getElementById('discount').value);

    const timeDifference = endDate.getTime() - startDate.getTime();
    const numberOfNights = timeDifference / (1000 * 3600 * 24);

    document.getElementById('nombre_nuits').value = numberOfNights;
    document.getElementById('total').value = (numberOfNights * tarifNuitee) - discount;
}
</script>


	<!--<script>
	
		selectedAppartment.addEventListener('change', function () {
			let apartment_name = selectedAppartment.value;
			if (apartment_name) {
				// Use fetch API to get the night price
				fetch('/php/get_night_price.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					},
					body: 'apartment_name=' + encodeURIComponent(apartment_name)
				})
					.then(response => response.text())
					.then(night_price => {
						document.getElementById('tarif_nuitee').value = night_price;
						calculerNombreNuits();
					});
			} else {
				document.getElementById('tarif_nuitee').value = '';
				calculerNombreNuits();
			}
		});
	</script>-->
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