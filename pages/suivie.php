<?php
session_start();
// Se connecter à la base de données
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
	header('Location: /pages/login.php');
	exit;
}

// Vérifiez si l'utilisateur a le bon rôle pour accéder à cette page

if (!(in_array('admin', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles']))) {
	header('Location: /pages/access_denied.php'); // Redirigez vers une page d'accès refusé
	exit;
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Suivie des Appartements | GBABLÉ</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
		integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
	<?php include('/home2/babimors/gbable.motorsfeere.com/pages/header.php') ?>
	<div class="container mt-5">
		<div class="row">
			<div class="col-md-12 ">
				<div class="card">
					<div class="card-header  text-center mb-4">
						<h1>Dépense par appartement</h1>
					</div>
					<div class="card-body">
						<form action="" method="post" class="form-inline">
							<div class="form-group mb-2">
								<label for="appartement" class="mr-2"><b>Choisir un appartement :</b></label>
								<select name="appartement" id="appartement" class="form-control" required>
									<option value="" disabled selected>Choisir un appartement</option>
									<option value="A4">A4</option>
                                <option value="A5">A5</option>
                                <option value="A6">A6</option>
                                <option value="A8">A8</option>
                                <option value="Spaceroom">Spaceroom</option>
                                <option value="Centrum">Centrum</option>
                                <option value="Bliss1">BLISS I</option>
                                <option value="Oasis1">OASIS I</option>
                                <option value="Vernoise1">Vernoise I</option>
                                <option value="Vernoise2">Vernoise II</option>
                                <option value="Lacotiere1">La Côtière I</option>
                                <option value="autre">Autre</option>
                                <option value="tmtm">Transfert Membre à Membre</option>
								</select>
							</div>
							<button type="submit" class="btn btn-primary mb-2 ml-2">Afficher les factures</button>
						</form>

						<?php
						// Vérifier si l'appartement est sélectionné
						if (isset($_POST['appartement'])) {
							// Récupérer l'appartement sélectionné
							$appartement = $_POST['appartement'];



							// Récupérer les factures de l'appartement sélectionné
							$sql = "SELECT * FROM depense WHERE appartement = :appartement ORDER BY date_insertion DESC";
							$requete = $connexion->prepare($sql);
							$requete->bindParam(':appartement', $appartement);
							$requete->execute();

							// Vérifier s'il y a des factures à afficher
							if ($requete->rowCount() > 0) {
								// Afficher les factures dans un tableau
								?>
								<div class="table-responsive">
									<table class="table table-striped table-hover">
										<thead class="thead-dark">
											<tr>
												<th>Date De Paiement</th>
												<th>Date Justificatif</th>
												<th>Désignation</th>
												<th>Montant</th>
												<th>Appartement</th>
												<th>Fait par</th>
												<th>Image</th>
											</tr>
											<?php while ($facture = $requete->fetch()) { ?>
												<tr>
													<td>
														<?php echo $facture['date_paiement']; ?>
													</td>
													<td>
														<?php echo $facture['date_justificatif']; ?>
													</td>
													<td>
														<?php echo $facture['designation']; ?>
													</td>
													<td>
														<?php echo $facture['montant']; ?>Fcfa
													</td>
													<td>
														<?php echo $facture['appartement']; ?>
													</td>
													<td>
														<?php echo $facture['author']; ?>
													</td>
													<td><a href='/images/imageRecu/<?php echo $facture['image']; ?>' target='_blank'>Voir l'image</a></td>
													<?php //echo $facture['image']; ?>
												</tr>
											<?php } ?>
									</table>
								</div>
							<?php } else { ?>
								<p>Aucune dépense à afficher pour l'appartement "
									<?php echo " ", $appartement ?>."
								</p>
							<?php }





						}
						?>





						<hr>
						<hr>
						<?php
						$sql = "SELECT * FROM encaissement  ORDER BY date_insertion DESC ";
						// Prepare and execute the SQL statement
						$requete = $connexion->prepare($sql);
						$requete->execute();

						if ($requete->rowCount() > 0) {
							// Afficher les factures dans un tableau
							?>
							<h1>Dernier encaissement </h2>
							<div class="table-responsive">
								<table class="table table-striped table-hover">
									<thead class="thead-dark">
										
											<tr>
												<th>Date De Paiement</th>
												<th>Facture</th>
											
												<th>Montant</th>
												<th>Reçu par</th>
											</tr>
											<?php while ($facture = $requete->fetch()) { ?>
												<tr>
													<td>
														<?php echo $facture['date_paiement']; ?>
													</td>
													<td>
														<?php echo $facture['invoice_number']; ?>
													</td>
													
													<td>
														<?php echo $facture['montant_paye']; ?>
													</td>
													</td>
													<td>
														<?php echo $facture['author']; ?>
													</td>
													<?php //echo $facture['image']; ?>
												</tr>
											<?php } ?>
								</table>
							</div>
						<?php } else { ?>
							<p>Aucun Encaissement à afficher pour l'appartement "
								<?php echo $appartement ?>."
							</p>
							<?php

							// Fermer la connexion à la base de données
							$connexion = null;
						} ?>

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