<?php
session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");


require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdfData = $_POST['pdfData'];
    $pdfData = base64_decode(substr($pdfData, strpos($pdfData, ",") + 1));
    $email = $_POST['email'];
    $appartement = $_POST['appartement'];
    $author = $_SESSION['user_firstname'];
    $numero_facture = $_POST['numero_facture'];
    $message = $_POST['message'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_paiement = $_POST['date_paiement'];
    $montant_paye = $_POST['montant_paye'];
    $userEmail = $_SESSION['user_email'];
    
    

    // Save the PDF to a temporary file
    $tempPdfPath = sys_get_temp_dir() . '/facture-' . time() .$filename. '.pdf';
    file_put_contents($tempPdfPath, $pdfData);

        //Send notification by email 
		// Créez une nouvelle instance de PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
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
    $mail->setFrom('info@gbable.motorsfeere.com', ' Gbablé');
    $mail->AddBCC('ibrahim.gbanet@xn--gbabl-fsa.com', 'Administrator');
	$mail->AddBCC('raissa.sangare@xn--gbabl-fsa.com', 'Comptabilité');
	$mail->AddBCC("{$userEmail}", '');
	$mail->addAddress("{$email}", '');

     // Attachments
     $mail->addAttachment($tempPdfPath, 'invoice.pdf'); // Add the PDF attachment
     // Contenu de l'email
    $mail->isHTML(false); // Format texte brut
if($message == "newFct"){
    $mail->Subject = "Réservation {$appartement} - {$nom} - {$prenom}";
    $mail->Body = "Hello {$prenom},\n\nVoici un résumé de votre réservation :\n";
    $mail->Body .= "Appartement : {$appartement}\n";
    $mail->Body .= "Effectuée par : {$author}\n";
    $mail->Body .= "Vous trouverez toutes les informations dans le fichier joint.\n\nCordialement,\nL'équipe Gbablé";

} else if ($message == "encaissement") {

    $mail->Subject = "Confirmation de paiement | Réservation - {$appartement} - {$nom} - {$prenom}";
    $mail->Body = "Hello {$prenom},\n\nVoici la confirmation de votre paiement :\n";
    $mail->Body .= "Date de paiement : {$date_paiement}\n";
    $mail->Body .= "Numéro de facture : {$numero_facture}\n";
    $mail->Body .= "Montant payé : {$montant_paye} Fcfa\n";
    $mail->Body .= "Reçu par : {$author}\n";
    $mail->Body .= "Vous trouverez tous les détails de votre réservation dans le fichier joint.\n";
    $mail->Body .= "Nous vous remercions pour votre confiance.\n\n";
    $mail->Body .= "Cordialement,\nL'équipe Gbablé";


}

    $target_dir = "/home2/babimors/gbable.motorsfeere.com/images/facture/"; // Change this to the path of the folder where you save the PDFs.
    $target_file = $target_dir . $numero_facture . ".pdf"; // Assuming the PDFs are named by their invoice number.
    
    // Check if a PDF with the same invoice number exists.
    if (file_exists($target_file)) {
        // Delete the old PDF.
        unlink($target_file);
    }
    
    // Save the new PDF.
    $pdfData = $_POST['pdfData'];
    $decodedPdfData = base64_decode(substr($pdfData, strpos($pdfData, ',') + 1));
    file_put_contents($target_file, $decodedPdfData);
  



    // Envoi de l'email
   $emailsent =  $mail->send();
} catch (Exception $e) {
    // L'email n'a pas pu être envoyé
    echo "Une erreur est survenue lors de l'envoi de l'email de notification : {$mail->ErrorInfo}";
}
} else {
    echo "Invalid request";
}
?>
