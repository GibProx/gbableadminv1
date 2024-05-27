<?php
session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/login.php');
    exit;
}


$firstname = $_SESSION['user_firstname'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard| GBABLÉ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    
    <style>
    .status-pending {
        color: orange;
    }

    .status-confirmed,
    .status-paid {
        color: green;
    }

    .status-canceled {
        color: red;
    }
</style>

    
</head>
<body>
<div class="container">
    <header>
        <h1 class="my-4">Dashboard | GBABLÉ</h1>
    </header>
    <main>
        <?php
        // Check if the user is logged in
        if (isset($_SESSION['user_id'])) {
            $utilisateur_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM users WHERE id = :id";
            $requete = $connexion->prepare($sql);
            $requete->bindParam(':id', $utilisateur_id);
            $requete->execute();

            if ($requete->rowCount() > 0) {
                $utilisateur = $requete->fetch();
                $nom_complet = $utilisateur['first_name'] . " " . $utilisateur['last_name'];
                ?>
                <!-- Display the welcome message -->
                <section>
                    <h2 class="mb-3">Bienvenue,
                        <?php echo $nom_complet ?> !
                    </h2>
                    <p>Que voulez-vous faire aujourd'hui ?</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php if (in_array('admin', $_SESSION['user_roles'])) { ?>
                            <a href='/pages/add_Property.php' class='btn btn-lg btn-block btn-outline-primary'>Ajouter un Appartement</a>
                        <?php } ?>
                        <?php if (in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles'])) { ?>
                            <a href='/pages/ajout_depense.php' class='btn btn-lg btn-block btn-outline-primary'>Ajouter une dépense</a>
                        <?php } ?>
                        <?php if (in_array('admin', $_SESSION['user_roles']) || in_array('booking', $_SESSION['user_roles']) || in_array('marketing', $_SESSION['user_roles'])) { ?>
                            <a href='/pages/new_Fct.php' class='btn btn-lg btn-block btn-outline-primary'>Créer une nouvelle facture</a>
                        <?php } ?>
                         
                        <?php if (in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles'])|| in_array('booking', $_SESSION['user_roles']) ) { ?>
                            <?php if (!in_array('marketing', $_SESSION['user_roles']) ) { ?>
                            <a href='/pages/ajout_encaissement.php' class='btn btn-lg btn-block btn-outline-primary'>Ajouter un encaissement</a>
    
                            
                        <?php }} ?>
                        
                    
                        <?php if (in_array('admin', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles']) ) { ?>
                            <a href='/pages/suivie.php' class='btn btn-lg btn-block btn-outline-primary'>Voir le suivi</a>
                        <?php } ?>
                        <a class='btn btn-danger' href="/php/logout.php">Déconnexion</a>
						</div>
					</section>
                <?php
            } 

         } 

if (in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles']) || in_array('booking', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles']) || in_array('marketing', $_SESSION['user_roles']) ){


					// Reservation
					// Récupérer les depense de l'appartement sélectionné
					
					$sql = "SELECT * FROM invoice   ORDER BY date_insertion DESC ";
				if (in_array('marketing', $_SESSION['user_roles'])){
				    $sql = "SELECT * FROM invoice WHERE author_Id='5'  ORDER BY date_insertion DESC ";
				}
					$requete = $connexion->prepare($sql);
					$requete->execute();

					// Vérifier s'il y a des factures à afficher
					if ($requete->rowCount() > 0) {
						// Afficher les factures dans un tableau
						?>
						<div class="table-responsive">
							<table class="table table-striped table-hover  ">
								<h1>Dernière Réservation</h1>
								<thead class="table-dark">
									<tr>
										<th>N0.Facture</th>
										<th>Appartement</th>
										<th>Client</th>
										<th>Date D'entrée</th>
										<th>Date De sortie</th>
										<th>Statut</th>
										<th>Facture</th>
									</tr>
								</thead>
								<tbody>
									<?php while ($invoice = $requete->fetch()) { ?>
                                        <?php
                                        $statusClass = '';
                                    
                                        switch ($invoice["status"]) {
                                            case 'Pending':
                                                $statusClass = 'status-pending';
                                                break;
                                            case 'Confirmed':
                                            case 'Paid':
                                                $statusClass = 'status-confirmed';
                                                break;
                                            case 'Cancelled':
                                                $statusClass = 'status-canceled';
                                                break;
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $invoice['invoice_number']; ?></td>
                                            <td><?php echo $invoice['appartement']; ?></td>
                                            <td><?php echo $invoice['prenom'] . " " . $invoice['nom']; ?></td>
                                            <td><?php echo dateFormatter($invoice['date_entree']); ?></td>
                                            <td><?php echo dateFormatter($invoice['date_sortie']); ?></td>
                                            <td class="<?php echo $statusClass; ?>"><?php echo $invoice["status"]; ?></td>
                                            <td><a href='/images/facture/<?php echo $invoice['invoice_number']; ?>.pdf' target='_blank'>Voir la Facture</a></td>
                                        </tr>
                                    <?php } ?>
								</tbody>
							</table>
						</div>

					<?php } else { ?>
						<p>Aucune facture à afficher pour l'appartement "
							<?php echo " ", $appartement ?>."
						</p>
					<?php }

					?>
					<?php
				
			}
			if (in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles']) || in_array('booking', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles'])){
				// Récupérer les depense de l'appartement sélectionné
				if (!(in_array('admin', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles'] ))){
					$sql = "SELECT * FROM depense WHERE author = :firstname  ORDER BY date_insertion DESC LIMIT 3";
				} else {
					$sql = "SELECT * FROM depense  ORDER BY date_insertion DESC LIMIT 3";

				}
				
									// Prepare and execute the SQL statement
				$requete = $connexion->prepare($sql);

				if (!(in_array('admin', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles'] ) )) {
					$requete->bindParam(':firstname', $firstname);
				}

				$requete->execute();

					// Vérifier s'il y a des factures à afficher
					if ($requete->rowCount() > 0) {
						// Afficher les factures dans un tableau
						?>
						<div class="table-responsive">
							<table class="table table-striped table-hover ">
									<h1>Dernière Dépense</h1>
								<thead class="table-dark">
									<tr>
										<th>Date De Paiement</th>
										<th>Date Justificatif</th>
										<th>Désignation</th>
										<th>Montant</th>
										<th>Appartement</th>
										<th>Fait par</th>
										<th>Reçu</th>
									</tr>
								</thead>
								<tbody>
									<?php while ($facture = $requete->fetch()) { ?>
										<tr>
											<td><?php echo $facture['date_paiement']; ?></td>
											<td><?php echo $facture['date_justificatif']; ?></td>
											<td><?php echo $facture['designation']; ?></td>
											<td><?php echo $facture['montant']; ?>Fcfa</td>
											<td><?php echo $facture['appartement']; ?></td>
											<td><?php echo $facture['author']; ?></td>
											<td><a href='/images/imageRecu/<?php echo $facture['image']; ?>' target='_blank'>Voir le Reçu</a></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					
						
					<?php } else { ?>
						<p>Aucune Dépense à afficher "
							<?php echo " ", $appartement ?>."
						</p>
					<?php }
			}
			if (in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles']) || in_array('booking', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles'])){
					// Récupérer les depense de l'appartement sélectionné
					if (!(in_array('admin', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles'] ) )){
						$sql = "SELECT * FROM encaissement WHERE author = :firstname  ORDER BY date_insertion DESC LIMIT 3";
					} else {
						$sql = "SELECT * FROM encaissement  ORDER BY date_insertion DESC LIMIT 3";

					}
					
										// Prepare and execute the SQL statement
					$requete = $connexion->prepare($sql);

					if (!(in_array('admin', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles'] ) )) {
						$requete->bindParam(':firstname', $firstname);
					}

					$requete->execute();

					// Vérifier s'il y a des factures à afficher
					if ($requete->rowCount() > 0) {
						// Afficher les factures dans un tableau
						?>
						<div class="table-responsive">
							<table class="table table-striped table-hover ">
									<h1>Derniers Encaissement</h1>
								<thead class="table-dark">
									<tr>
										<th>Date De Paiement</th>
										<th>Facture</th>
								
										<th>Montant</th>
										<th>Reçu par</th>
									</tr>
								</thead>
								<tbody>
									<?php while ($facture = $requete->fetch()) { ?>
										<tr>
											<td><?php echo $facture['date_paiement']; ?></td>
											<td><?php echo $facture['invoice_number']; ?></td>
											<td><?php echo $facture['montant_paye']; ?></td>
											<td><?php echo $facture['author']; ?></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>

					<?php } 

					?>
					<?php
			}?>

            
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybBud7TTOXcLdvZDzKgH_SG01E1MsdMsA6Ee7ayDyDsi2g5z9" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
