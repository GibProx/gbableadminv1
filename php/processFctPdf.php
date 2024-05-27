<?php
session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");
require_once('/home2/babimors/gbable.motorsfeere.com/php/TCPDF/tcpdf.php');

require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $appartement = $_POST['appartement'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $date_entree = $_POST['date_entree'];
    $date_sortie = $_POST['date_sortie'];
    $tarif_nuitee = $_POST['tarif_nuitee'];
    $nombre_nuits = $_POST['nombre_nuits'];
    $total = $_POST['total'];
    $author = $_SESSION['user_firstname'];
    $numero_facture ="######";
    $filename = "$numero_facture $appartement | $nom-$prenom.pdf";
    
   



    // Prepare an INSERT statement
    $stmt = $connexion->prepare("INSERT INTO invoice (appartement, nom, prenom, telephone, email, date_entree, date_sortie, tarif_nuitee, nombre_nuits, total, nom_facture, remaining_balance) VALUES (:appartement, :nom, :prenom, :telephone, :email, :date_entree, :date_sortie, :tarif_nuitee, :nombre_nuits, :total, :nom_facture, :remaining_balance)");
    // Bind parameters
    $stmt->bindParam(':appartement', $appartement);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':date_entree', $date_entree);
    $stmt->bindParam(':date_sortie', $date_sortie);
    $stmt->bindParam(':tarif_nuitee', $tarif_nuitee);
    $stmt->bindParam(':nombre_nuits', $nombre_nuits);
    $stmt->bindParam(':total', $total);
    $stmt->bindParam(':nom_facture', $filename); 
    $stmt->bindParam(':remaining_balance', $total);
    // Execute the statement
    $stmt->execute();
    $x = 1;
    if ($x = 1) {
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Facture | ' . $appartement . ' | ' . $nom . '-' . $prenom);

        // Remove default header/footer
        $pdf->setHeaderData(false);
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(15, 15, 15);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 0);

        // Add a page
        $pdf->AddPage();
        // Add the logo
        $logo_path = '/images/gbableLogo.png';
        $logo_width = 150;
        //$pdf->Image($logo_path, 5, $pdf->GetY(), $logo_width);
        $pdf->Image('/images/gbableLogo.jpg', 10, 10, -300);
        //Page header
        


        // Définir la taille de la police par défaut
        $pdf->SetFontSize(11);

        //Time Formatter

       

        $date_entreeF = dateFormatter($date_entree);
        $date_sortieF = dateFormatter($date_sortie);
        $dateTime = date('y-m-d ');
        $dateTimeF = dateFormatter($dateTime);

        // Caution declaration
        if ($nombre_nuits > 3) {
            $caution = get_caution_by_name($connexion, $appartement);
        } else {
            $caution = get_caution_by_name($connexion, $appartement) / 2;
        }
        $formatted_caution = number_format($caution, 0, '', ' ');

        // Address de l'appartement 

        $address = get_address_by_name($connexion, $appartement);

        // total a payer caution + cout sejour
        $toPay = $caution + intval($total);
        $formatted_toPay = number_format($toPay, 0, '', ' ');
        $formatted_total = number_format($total, 0, '', ' ');
        
        $formatted_tarif_nuitee = number_format($tarif_nuitee, 0, '', ' ');

        // Reste a payer 

        $balance = $toPay;
        $formatted_balance = number_format($balance, 0, '', ' ');


        $image_path = 'gbableLogo.png';

        $print = '<img src=" ' . $image_path . ' ">';
        // Define invoice content
        $content = "
    <p class='p1'>Facture : {$numero_facture} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Date :{$dateTimeF}</p>
    {$print}
    <h1 class='p2' valign='middle'><strong><span class='s1'>R&eacute;servation : {$appartement}</span></strong></h1>

    <p class='p4'><span class='s1'><h2><strong>Adresse</strong> :</h2></span>{$address}<span class='Apple-converted-space'>&nbsp;</span></p>
    <h2 class='p5'><span class='s1'><strong>Coordonn&eacute;es du client :</strong></span></h2>
    <p class='p5'><strong>Nom</strong>: {$nom}<span class='Apple-converted-space'>&nbsp;&nbsp;</span><strong>Pr&eacute;nom</strong>: {$prenom}</p>
    <p class='p5'><strong>Num&eacute;ro</strong>: {$telephone}<span class='Apple-converted-space'>&nbsp;&nbsp;</span><strong>Email</strong>: {$email}</p>
    <h2 class='p5'><span class='s1'>D&eacute;tails de la R&eacute;servation&nbsp;</span>:</h2>
   
    <table>
        <tbody>
            <tr>
                <td class='td1' valign='middle'>
                    <p class='p7'><strong>DATE</strong></p>
                </td>
                <td class='td2' valign='middle'>
                    <p class='p8'><strong>{$date_entreeF}</strong></p>
                </td>
                <td class='td3' valign='middle'>
                    <p class='p8'><strong>{$date_sortieF}</strong></p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <table>
        <tbody>
            <tr>
                <td valign='middle'>
                    <p class='p9'>Nombre de nuits</p>
                </td>
                <td class='td5' valign='middle'>
                    <p class='p10'>Prix unitaire</p>
                </td>
                <td class='td6' valign='middle'>
                    <p class='p10'>Montant</p>
                </td>
            </tr>
            <tr>
                <td valign='middle'>
                    <p class='p10'>{$nombre_nuits}</p>
                </td>
                <td class='td5' valign='middle'>
                    <p class='p10'>{$formatted_tarif_nuitee}Fcfa</p>
                </td>
                <td class='td7' valign='middle'>
                    <p class='p11'>{$formatted_total}Fcfa</p>
                </td>
            </tr>
            
            <tr>
                <td class='td11' colspan='2' valign='middle'>
                    <p class='p9'>Caution</p>
                </td>
                <td class='td12' valign='middle'>
                    <p class='p13'></p>
                </td>
                <td class='td12' valign='middle'>
                    <p class='p13'>{$formatted_caution}Fcfa</p>
                </td>
            </tr>
        </tbody>
    </table>
    <table  >
        <tbody>
            <tr>
            <td class='td13' valign='middle'>
                    <p class='p14'></p>
                </td>
                <td class='td13' valign='middle'>
                    <p class='p14'><strong>Total:</strong></p>
                </td>
                <td class='td14' valign='middle'>
                    <p class='p14'>{$formatted_toPay}Fcfa</p>
                </td>
                
            </tr>
        </tbody>
    </table>
  
    <table  >
        <tbody>
            <tr>
                <td class='td16' valign='middle'>
                    <p class='p4'>Montant pay&eacute; :</p>
                </td>
                <td class='td17' valign='middle'>
                    <p class='p16'>0</p>
                </td>
                <td class='td18' valign='middle'>
                    <p class='p4'>Fcfa</p>
                </td>
                <td class='td19' valign='middle'>
                    <p class='p17'></p>
                </td>
            </tr>
            
            <tr>
                <td class='td24' valign='middle'>
                    <p class='p4'><strong>Reste &agrave; payer :</strong></p>
                </td>
                <td class='td25' valign='middle'>
                    <p class='p16'><strong>{$formatted_balance}</strong></p>
                </td>
                <td class='td26' valign='middle'>
                    <p class='p4'><strong>Fcfa</strong></p>
                </td>
                <td class='td23' valign='middle'>
                    <p class='p15'><br></p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <p class='p18'><strong>Check-In : 14h&nbsp;</strong><span class='s2'><strong>|</strong></span><strong>&nbsp;Check-Out : 12h</strong></p>
    <p class='p19'>NB: Un paiement <strong>minimum de 50% du total*</strong> est requis pour la confirmation de la r&eacute;servation.</p>
    <p>Contact :&nbsp;</p>
    <p><strong>Sur place/WhatsApp&nbsp;</strong>: +225 070 816 8284 &mdash; <strong>Seulement</strong> <strong>WhatsApp</strong> : +225 070 128 2929&nbsp;</p>
    <p><strong>E-mail</strong> : info@gbabl&eacute;.com &nbsp; &nbsp;</p>
    
    <h2>Clause de Paiement :</h2>
	<p>Le locataire s'engage à effectuer un paiement initial de 50% du montant total de la réservation au moment de la réservation afin de bloquer les dates demandées.</p>
	<p>Le paiement initial peut être effectué par virement bancaire, carte de crédit ou tout autre moyen de paiement convenu entre les parties. Les informations nécessaires pour effectuer ce paiement seront fournies au locataire lors de la confirmation de la réservation.</p>
	<p>Le montant restant, correspondant à 50% du montant total de la réservation, devra être réglé à l'arrivée du locataire, avant la remise des clés et l'entrée dans le logement.</p>
	<p>Le paiement du montant restant peut être effectué en espèces, par virement bancaire, par carte de crédit ou tout autre moyen de paiement accepté par le propriétaire. Les détails concernant les modes de paiement acceptés seront communiqués au locataire avant son arrivée.</p>
	<p>En cas de non-paiement du montant restant dû à l'arrivée, le propriétaire se réserve le droit de refuser l'accès au logement et d'annuler la réservation conformément aux conditions d'annulation spécifiées dans le contrat.</p>
	<p>Tous les frais bancaires ou de transaction liés aux paiements sont à la charge du locataire.</p>

	<h2>Annulation et remboursement :</h2>
	<p>En cas d'annulation de la réservation par le locataire, les modalités de remboursement seront les suivantes:</p>
	<ul>
		<li>Annulation 5 jours avant la date d'arrivée : Remboursement intégral de l'acompte</li>
		<li>Annulation moins de 5 jours avant la date d'arrivée : Aucun remboursement de l'acompte</li>
	</ul>

	<h2>Caution :</h2>
	<p>Le locataire devra fournir une caution (voir sommaire de la réservation) à l'arrivée, qui sera restituée dans un délai de 14 jours après la fin de la location, déduction faite des éventuels dommages ou frais supplémentaires.</p>

	<h2>Clause de Changement de Réservation :</h2>
	<p>Toute demande de modification de la réservation initiale, telle que les changements de dates ou de nombre de personnes, doit être soumise par écrit et approuvée par le propriétaire.</p>
	<p>Le propriétaire fera de son mieux pour accommoder les demandes de modification de réservation, sous réserve de disponibilité et de conditions éventuelles supplémentaires.</p>
	<p>Des frais supplémentaires peuvent s'appliquer en cas de changement de réservation, conformément aux tarifs en vigueur au moment de la demande de modification.</p>

	<h2>Clause d'Interruption de Séjour :</h2>
	<p>En cas d'interruption prématurée du séjour par le locataire, aucun remboursement ne sera effectué pour les jours non utilisés, sauf disposition contraire spécifiée dans cette clause.</p>
	<p>Dans des circonstances exceptionnelles telles que des événements majeurs ou des problèmes majeurs de logement rendant le séjour impossible ou dangereux, le propriétaire se réserve le droit d'interrompre le séjour du locataire. Dans ce cas, le propriétaire remboursera au locataire la partie non utilisée du loyer.</p>

	<h2>Règles et responsabilités :</h2>
	<p>Le locataire s'engage à respecter les règles suivantes:</p>
	<ul>
		<li>Ne pas fumer à l'intérieur du logement.</li>
		<li>Ne pas autoriser les animaux de compagnie sans autorisation préalable.</li>
		<li>Respecter les horaires de calme et éviter tout comportement nuisible pour les voisins.</li>
		<li>Utiliser les équipements du logement de manière appropriée et suivre les instructions de sécurité.</li>
	</ul>

	<h2>Responsabilités du locataire :</h2>
	<p>Le locataire est responsable des éléments suivants:</p>
	<ul>
		<li>Maintenir le logement propre et en bon état pendant la durée de la location.</li>
		<li>Informer immédiatement le propriétaire de tout dommage ou incident survenu dans le logement.</li>
		<li>Respecter les consignes de sécurité et d'urgence fournies par le propriétaire.</li>
	</ul>

	<h2>Limitation d'occupation :</h2>
	<p>Le nombre maximum de personnes autorisées à occuper le logement est de [nombre de personnes]. Toute personne supplémentaire devra être approuvée par le propriétaire.</p>

	<h2>Remise des clés :</h2>
	<p>Le propriétaire remettra les clés au locataire à l'arrivée. Le locataire devra restituer les clés au propriétaire à la fin de la location.</p>

	<h2>Clause de Responsabilité du Propriétaire :</h2>
	<p>Le propriétaire n'assume aucune responsabilité pour les accidents, blessures, maladies ou pertes subis par le locataire ou ses invités pendant la durée de la location.</p>
	<p>Le locataire est responsable de la sécurité de ses biens personnels pendant la durée de la location. Le propriétaire décline toute responsabilité en cas de vol, de perte ou de dommages causés aux biens personnels du locataire.</p>
	<p>Le propriétaire n'assume aucune responsabilité pour les interruptions de services publics, les pannes d'équipement ou les nuisances temporaires indépendantes de sa volonté, telles que les coupures d'électricité, les pannes de plomberie ou les problèmes de connexion Internet.</p>
	<p>Le locataire est tenu de signaler immédiatement au propriétaire tout problème ou dommage survenu dans le logement pendant la durée de la location.</p>

	<h2>Loi applicable et juridiction compétente :</h2>
	<p>Ce contrat est régi par les lois en vigueur dans [pays/région]. Tout litige découlant de ce contrat sera soumis à la juridiction des tribunaux de [ville/pays].</p>
    ";



        // Write the content to the PDF
        $pdf->writeHTML($content, true, false, true, false, '');

        // Close and output PDF document
        $filename = "Facture | $appartement | $nom-$prenom.pdf";
        $pdf->Output($filename, 'I');

        // Save the PDF to a temporary file
$tempPdfPath = sys_get_temp_dir() . '/facture-' . time() . '.pdf';
$pdf->Output($tempPdfPath, 'F');

        //Send notification by email 
		// Créez une nouvelle instance de PHPMailer
$mail = new PHPMailer(true);

try {
    // Paramètres du serveur
    $mail->isSMTP(); // Utilisez SMTP
    $mail->Host = 'smtp-mail.outlook.com'; // Adresse du serveur SMTP
    $mail->SMTPAuth = true; // Active l'authentification SMTP
    $mail->Username = 'gib_@outlook.com'; // Adresse e-mail de l'expéditeur
    $mail->Password = '1Abidjan'; // Mot de passe de l'expéditeur
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Active l'encryption TLS (ou SSL selon les besoins)
    $mail->Port = 587; // Port pour la connexion SMTP
    $mail->CharSet = 'UTF-8';

    // Destinataires
    $mail->setFrom('gib_@outlook.com', 'Application Gbablé');
    $mail->addAddress('ibrahim.gbanet@xn--gbabl-fsa.com', 'Administrator');
	$mail->addAddress('raissa.sangare@xn--gbabl-fsa.com', 'Comptabilité');
	$mail->addAddress("{$email}", '');

     // Attachments
     $mail->addAttachment($tempPdfPath, $filename); // Add the PDF attachment

    // Contenu de l'email
    $mail->isHTML(false); // Format texte brut
    $mail->Subject = "Nouvelle réservation ajoutée par {$author}";
    $mail->Body    = "Bonjour,\n\nUne nouvelle facture a été créer :\n";
    $mail->Body   .= "Appartement : {$appartement}\n";
    $mail->Body   .= "Auteur : {$author}\n\nCordialement,\nL'application Gbablé";

    // Envoi de l'email
   $emailsent =  $mail->send();
} catch (Exception $e) {
    // L'email n'a pas pu être envoyé
    echo "Une erreur est survenue lors de l'envoi de l'email de notification : {$mail->ErrorInfo}";
}


if($emailsent){
// Success! Redirect to a confirmation page
header('Location: /pages/confirmation.php');
exit;
}
		
	} else {
		// Error
		echo "Une erreur est survenue lors de l'ajout de la facture.";
	}

	// Close the database connection
	$connexion = null;


    

    
}