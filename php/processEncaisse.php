<?php
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");

require_once('/home2/babimors/gbable.motorsfeere.com/php/TCPDF/tcpdf.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

 // Retrieve the form data newIncome
 $id_invoice = $_POST['id_invoice'];
 $date_paiement = $_POST['date_paiement'];
 $montant_paye = $_POST['montant_paye'];
 $email = $_SESSION['user_email'];
 $author = $_SESSION['user_firstname'];
 
 // Check the status of the invoice
 $status = check_status_invoice($connexion, $id_invoice);
 
 // If the status is 'Pending', generate a new invoice number and update the invoice status
 if ($status == 'Pending') {
     $numero_facture = generate_invoice_number($connexion);
     update_invoice_status($connexion, $id_invoice, 'Confirmed', $numero_facture);
 } else {
     // If the status is not 'Pending', retrieve the existing invoice number
     $numero_facture = get_invoice_number($connexion, $id_invoice);
 }
 // Prepare the SQL query to insert the data into the "encaissements" table
 $sql = "INSERT INTO encaissement (date_paiement, invoice_number, montant_paye, author) VALUES (:date_paiement, :numero_facture, :montant_paye, :author)";

 try {
     $stmt = $connexion->prepare($sql);
     $stmt->bindParam(':date_paiement', $date_paiement);
     $stmt->bindParam(':numero_facture', $numero_facture);
     $stmt->bindParam(':montant_paye', $montant_paye);
     $stmt->bindParam(':author', $author);
    $encaissementInserted = $stmt->execute();
 
     // Update the remaining balance for the invoice
     update_remaining_balance($connexion, $id_invoice, $montant_paye);
     
 } catch (PDOException $e) {
     die("Error: " . $e->getMessage());
 }
 
if ($encaissementInserted){

    
    $invoice = display_invoice_information($connexion, $id_invoice);
    $appartement = $invoice['appartement'];
    $nom = $invoice['nom'];
    $prenom = $invoice['prenom'];
    $telephone = $invoice['telephone'];
    $email = $invoice['email'];
    $date_entree = $invoice['date_entree'];
    $date_sortie = $invoice['date_sortie'];

    $tarif_nuitee = $invoice['tarif_nuitee'];
    $nombre_nuits = $invoice['nombre_nuits'];
    $total = $invoice['total'];
    $invoice['nom_facture'];
    $remaining_balance = $invoice['remaining_balance'];
    
    

    

     // Create new PDF document
     $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

     // Set document information
     $pdf->SetCreator(PDF_CREATOR);
     $pdf->SetTitle($numero_facture .' | ' . $appartement . ' | ' . $nom . '-' . $prenom);

     // Remove default header/footer
  
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

     function dateFormatter($inputDate)
     {

         // Input date in the format YYYY-MM-DD


         // Create a DateTime object from the input date
         $dateTime = new DateTime($inputDate);

         // Set the desired locale for the output format
         // In this case, we use 'fr_FR' for French
         $locale = 'fr_FR';

         // Create an IntlDateFormatter object with the desired format
         $formatter = new IntlDateFormatter(
             $locale,
             IntlDateFormatter::LONG, // Use LONG date format
             IntlDateFormatter::NONE,
             // No time format
             'UTC', // Use UTC timezone
             IntlDateFormatter::GREGORIAN,
             // Use Gregorian calendar
             'd MMMM yyyy' // Custom pattern to match the desired output
         );

         // Format the date using the formatter
         $outputDate = $formatter->format($dateTime);

         // Print the formatted date
         return $outputDate;

     }
     ;

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

     $balance = $remaining_balance;
     $formatted_balance = number_format($balance, 0, '', ' ');


     $image_path = 'gbableLogo.png';

     $print = '<img src=" ' . $image_path . ' ">';

     //paiement effectuer
     $encaissements = get_encaissements_by_invoice($connexion, $numero_facture);
      // Generate the encaissements table content
$table_content = '';
foreach ($encaissements as $encaissement) {
    $table_content .= "<tr>
    <td class='td18' valign='middle'>
            <p class='p4'></p>
        </td>
        <td class='td17' valign='middle'>
            <p class='p16'>" . number_format($encaissement['montant_paye'], 0, '', ' ') .  "Fcfa</p>
        </td>
        
        <td class='td19' valign='middle'>
            <p class='p17'>" . $encaissement['date_paiement'] . "</p>
        </td>
    </tr>";
}
    
     // Define invoice content
     $content = "
 <p class='p1'>Facture : {$invoice['invoice_number']} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Date :{$dateTimeF}</p>
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
                 <p class='p14'><strong>{$formatted_toPay}Fcfa</strong></p>
             </td>
             
         </tr>
     </tbody>
 </table>
 <p class='p15'></hr></p>
 <table  >
     <tbody>
     <thead>
        <tr>
            <th class='td16'><strong>Montant payé</strong></th>
            <th class='td17'></th>
            <th class='td19'><strong>Date de paiement</strong></th>
        </tr>
         {$table_content}
         
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
 ";



     // Write the content to the PDF
     $pdf->writeHTML($content, true, false, true, false, '');

     // Close and output PDF document
     $filename = "#$numero_facture | $appartement | $nom-$prenom.pdf";
     $pdf->Output($filename, 'I');
     //UPDATE INVOICE NAME
     $newFilename = "#$numero_facture | $appartement | $nom-$prenom Solde:$formatted_balance ";
     update_nom_facture($connexion, $id_invoice, $newFilename);
     // Save the PDF to a temporary file
$tempPdfPath = sys_get_temp_dir() . '/facture-' . time() . '.pdf';
$pdf->Output($tempPdfPath, 'F');
    // Send notification by email
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

        // Recipients
        $mail->setFrom('gib_@outlook.com', 'Application Gbablé');
        $mail->addAddress('ibrahim.gbanet@xn--gbabl-fsa.com', 'Administrator');
        $mail->addAddress('raissa.sangare@xn--gbabl-fsa.com', 'Comptabilité');
        $mail->addAddress("{$email}", '');

         // Attachments
     $mail->addAttachment($tempPdfPath, $filename); // Add the PDF attachment
        // Content
        $mail->isHTML(false);
        $mail->Subject = "Nouvel encaissement ajouté par {$author}";
        $mail->Body    = "Bonjour,\n\nUn nouvel encaissement a été ajouté :\n";
        $mail->Body   .= "Date de paiement : {$date_paiement}\n";
        $mail->Body   .= "Numéro de facture : {$numero_facture}\n";
        $mail->Body   .= "Montant payé : {$montant_paye}\n";
        $mail->Body   .= "Reçu par : {$author}\n\nCordialement,\nL'application Gbablé";

        // Send the email
        $emailsent = $mail->send();
    } catch (Exception $e) {
        // The email could not be sent
        echo "Une erreur est survenue lors de l'envoi de l'email de notification : {$mail->ErrorInfo}";
    }
    if ($emailsent) {
        // Redirect to a success page or display a success message
        header('Location: /pages/confirmation.php'); // Redirect to a success page (create success.html)
        exit;
    } else {
        // Error
        echo "Une erreur est survenue lors de l'ajout de l'encaissement.";
    }

    // Close the database connection
    $connexion = null;
}
} else {
    // Redirect to the main form if this script is accessed directly
    header('Location: index.html'); // Replace with the name of the HTML file containing your form
    exit;
}



?>