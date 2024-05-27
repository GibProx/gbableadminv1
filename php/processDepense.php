<?php
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
	header('Location: /pages/login.php');
	exit;
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// Get the form data
	$date_paiement = $_POST['date_paiement'];
	$date_justificatif = $_POST['date_justificatif'];
	$designation = $_POST['designation'];
	$montant = $_POST['montant'];
	$appartement = $_POST['appartement'];
	$author = $_SESSION['user_firstname'];
	$email = $_SESSION['user_email'];
	// Get the uploaded image
	$image = $_FILES['image'];

	// Create a target directory
	$target_dir = "/home2/babimors/gbable.motorsfeere.com/images/imageRecu/";

	// Generate a new file name
$uploaded_file_name = basename($image["name"]);
$sanitized_file_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $uploaded_file_name); // Replace special characters with underscores
$new_file_name = time() . '_' . $sanitized_file_name;


	// Set the target file path
	$target_file = $target_dir . $new_file_name;

	// Move the uploaded image to the target directory
	if (move_uploaded_file($image["tmp_name"], $target_file)) {
        echo "The file " . $new_file_name . " has been uploaded.";
   
	

	// Prepare the SQL statement
	$sql = "INSERT INTO depense (date_paiement, date_justificatif, designation, montant, appartement, author, image) VALUES (:date_paiement, :date_justificatif, :designation, :montant, :appartement, :author, :image)";
	$stmt = $connexion->prepare($sql);

	// Bind the form data to the SQL statement
	$stmt->bindParam(':date_paiement', $date_paiement);
	$stmt->bindParam(':date_justificatif', $date_justificatif);
	$stmt->bindParam(':designation', $designation);
	$stmt->bindParam(':montant', $montant);
	$stmt->bindParam(':appartement', $appartement);
	$stmt->bindParam(':author', $author);
	$stmt->bindParam(':image', $new_file_name);

	// Execute the SQL statement
	if ($stmt->execute()) {

		//Send notification by email 
		// Créez une nouvelle instance de PHPMailer
$mail = new PHPMailer(true);

try {
    /* Paramètres du serveur
    $mail->isSMTP(); // Utilisez SMTP
    $mail->Host = 'mail.motorsfeere.com';  // Adresse du serveur SMTP
    $mail->SMTPAuth = true; // Active l'authentification SMTP
    $mail->Username = 'gib_@outlook.com'; // Adresse e-mail de l'expéditeur
    $mail->Password = '1Abidjan'; // Mot de passe de l'expéditeur
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Active l'encryption TLS (ou SSL selon les besoins)
    $mail->Port = 	465; // Port pour la connexion SMTP
 */
 
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;  // debogage: 1 = Erreurs et messages, 2 = messages seulement
    $mail->SMTPAuth = true;  // Authentification SMTP active
    $mail->Host = 'gbable.motorsfeere.com';               //Adresse IP ou DNS du serveur SMTP
    $mail->Port = 	465;                          //Port TCP du serveur SMTP
    $mail->SMTPAuth = 1;                        //Utiliser l'identification
    $mail->CharSet = 'UTF-8';
    
    if($mail->SMTPAuth){
   $mail->SMTPSecure = 'ssl';               //Protocole de sécurisation des échanges avec le SMTP
   $mail->Username   = 'info@gbable.motorsfeere.com';    //Adresse email à utiliser
   $mail->Password   =  'y=^]l7qaC8q$';         //Mot de passe de l'adresse email à utiliser
}
    // Destinataires
    $mail->setFrom('info@gbable.motorsfeere.com', 'Gbablé');
    $mail->AddBCC('ibrahim.gbanet@xn--gbabl-fsa.com', 'Administrator');
	$mail->AddBCC('raissa.sangare@xn--gbabl-fsa.com', 'Comptabilité');
	$mail->addAddress("{$email}", '');

    // Contenu de l'email
    $mail->isHTML(false); // Format texte brut
    $mail->Subject = "Nouvelle dépense ajoutée par {$author}";
    $mail->Body    = "Bonjour,\n\nUne nouvelle dépense a été ajoutée :\n";
    $mail->Body   .= "Date de paiement : {$date_paiement}\n";
    $mail->Body   .= "Date du justificatif : {$date_justificatif}\n";
    $mail->Body   .= "Désignation : {$designation}\n";
    $mail->Body   .= "Montant : {$montant}\n";
    $mail->Body   .= "Appartement : {$appartement}\n";
    $mail->Body   .= "Éffectuer par : {$author}\n\nCordialement,\nL'équipe Gbablé";
   
    
    
    
    
  

    // Envoi de l'email
   $emailsent =  $mail->send();
} catch (Exception $e) {
    // L'email n'a pas pu être envoyé
    echo "Une erreur est survenue lors de l'envoi de l'email de notification : {$mail->ErrorInfo}";
}


if($emailsent){
// Success! Redirect to a confirmation page
return true;
}
		
	} else {
		// Error
		echo "Une erreur est survenue lors de l'ajout de la facture.";
	}

	// Close the database connection
	$connexion = null;
} else {
	echo "An error occurred while uploading the image.";
}
}



?>