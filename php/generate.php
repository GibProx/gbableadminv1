<?php
session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");
if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $ssl = 'https';
}
else {
  $ssl = 'http';
}
 
$app_url = ($ssl  )
          . "://".$_SERVER['HTTP_HOST']
          //. $_SERVER["SERVER_NAME"]
          . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
          . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");

//--->get app url > end

header("Access-Control-Allow-Origin: *");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['newFct'])){
        
   
    // Retrieve form data New fct------------------------------------------
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
    $discount = $_POST['discount'];
    //--------------
    $id_invoice = '0';
    $date_paiement = '0';
    $montant_paye = '0';
    //--------------//

    $folderPath = '/home2/babimors/gbable.motorsfeere.com/images/facture/'; // Replace with the path to your desired folder
    $numero_facture = generateUniqueFilename($folderPath);
   
// Retrieve form data New fct------------------------------------------END
} else if(isset($_POST['newIncome'])){
// Retrieve form data newIncome------------------------------------------
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
    
} else {
    // If the status is not 'Pending', retrieve the existing invoice number
    $numero_facture = get_invoice_number($connexion, $id_invoice);
}



    
    $invoice = display_invoice_information($connexion, $id_invoice);
    $appartement = $invoice['appartement'];
    $nom = $invoice['nom'];
    $prenom = $invoice['prenom'];
    $telephone = $invoice['telephone'];
    $email = $invoice['email'];
    $date_entree = $invoice['date_entree'];
    $date_sortie = $invoice['date_sortie'];
    $discount = $invoice['discount'];
    $tarif_nuitee = $invoice['tarif_nuitee'];
    $nombre_nuits = $invoice['nombre_nuits'];
    $total = $invoice['total'];
    $invoice['nom_facture'];
    $remaining_balance = $invoice['remaining_balance'];
    $filename = "$numero_facture $appartement | $nom-$prenom.pdf";
//paiement effectuer
$encaissements = get_encaissements_by_invoice($connexion, $numero_facture);
}
// Retrieve form data newIncome------------------------------------------END


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

//
$montantBeDicount = $total + $discount;
// Address de l'appartement 
$address = get_address_by_name($connexion, $appartement);
// total a payer caution + cout sejour
$toPay = $caution + intval($total);
$formatted_toPay = number_format($toPay, 0, '', ' ');
$formatted_total = number_format($total, 0, '', ' ');
$formatted_tarif_nuitee = number_format($tarif_nuitee, 0, '', ' '); 

    $table_content = '';
if (isset($encaissements) ) {
    
foreach ($encaissements as $encaissement) {
    $table_content .= "

                <tr>
                    <td>Montant payé:</td>
                    <td>". number_format($encaissement['montant_paye'], 0, '', ' ') ."  </td>
                    <td>" . $encaissement['date_paiement'] . "</td>
                </tr>";

               
    
}
$table_content .= "
                <tr>
                    <td>Montant payé:</td>
                    <td>".  number_format($montant_paye, 0, '', ' ') ." </td>
                    <td>" .  $date_paiement ."</td>
                </tr>";

$remaining_balance = $remaining_balance  - intval($montant_paye);
}else{
    $table_content .= "
                <tr>
                    <td>Montant payé:</td>
                    <td>0 Fcfa</td>
                    <td></td>
                </tr>
                
                ";

                $remaining_balance =  $toPay;
}

// Reste a payer 

$formatted_balance = number_format($remaining_balance, 0, '', ' ');



?>


<!DOCTYPE html>
<html>
<head>
	 
	<title> Template </title>

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="description" content="This ">

	<meta name="author" content="Code With Mark">
	<meta name="authorUrl" content="http://codewithmark.com">

	<!--[CSS/JS Files - Start]-->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">


	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script> 


	<script src="https://cdn.apidelv.com/libs/awesome-functions/awesome-functions.min.js"></script> 
  
   
 	<style>
		.container_content::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url("/images/LogoGbable.png");
            background-position: 96% 10%;
            background-repeat: no-repeat;
            background-size: 45%;
            z-index: -1;
        }


	</style>
     <!-- Add the SweetAlert2 library -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" ></script>

 

    <script type="text/javascript">
  $(document).ready(function () {
    $(document).on("click", ".btn_print", function (event) {
      event.preventDefault();

      // Disable the button
    $("#rep").prop("disabled", true);

      // Generate the PDF and get the PDF data as a base64 string
      const generatePdfData = () => {
        const element = document.getElementById("container_content");
        const opt = {
          filename: "<?php echo $filename ?>",
          image: { type: "jpeg", quality: 1 },
          html2canvas: { scale: 2 },
          jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
        };

        return html2pdf()
          .from(element)
          .set(opt)
          .outputPdf("datauristring");
      };

      // Send the base64-encoded PDF data via AJAX
      const sendPdfData = (pdfData, postData) => {
        return $.ajax({
          url: "/php/send_invoice.php",
          method: "POST",
          data: { pdfData, ...postData },
        });
      };

      if ("<?php echo isset($_POST['newFct']) ?>") {
        // Call AJAX to insert invoice data into the database
        $.ajax({
          url: "/php/insert_invoice.php",
          type: "POST",
          dataType: "json",
          data: {
            numero_facture: '<?php echo $numero_facture; ?>',
            appartement: '<?php echo $appartement; ?>',
            nom: '<?php echo $nom; ?>',
            prenom: '<?php echo $prenom; ?>',
            telephone: '<?php echo $telephone; ?>',
            email: '<?php echo $email; ?>',
            date_entree: '<?php echo $date_entree; ?>',
            date_sortie: '<?php echo $date_sortie; ?>',
            tarif_nuitee: '<?php echo $tarif_nuitee; ?>',
            nombre_nuits: '<?php echo $nombre_nuits; ?>',
            total: '<?php echo $total; ?>',
            totalToPay: '<?php echo $remaining_balance; ?>',
            filename: '<?php echo $filename; ?>',
            discount: '<?php echo $discount; ?>'
          },
          success: function (response) {
            if (response.status === "success") {
              generatePdfData().then((pdfData) => {
                const postData = {
                    pdfData: pdfData,
                    email: '<?php echo $email; ?>',
                    appartement: '<?php echo $appartement; ?>',
                    nom: '<?php echo $nom; ?>',
                    prenom: '<?php echo $prenom; ?>',
                    numero_facture: '<?php echo $numero_facture; ?>',
                    message: 'newFct',
                };
                sendPdfData(pdfData, postData).then((response) => {
        Swal.fire({
          title: "Facture Créer!",
          
          icon: "success",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Terminer",
          cancelButtonText: "Nouvelle facture",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "/index.php";
          } else {
            window.location.href = "/pages/new_Fct.php";
          }
        });
      });
    });
            } else {
                // Re-enable the button
        $("#req").prop("disabled", false);
              alert("Error1: " + response.message);
            }
          },
          error: function (xhr, status, error) {
            alert("Error2: " + error);
          },
        });
      } else if ("<?php echo isset($_POST['newIncome']) ?>") {
        // Call AJAX to insert payment data into the database
        $.ajax({
          url: "/php/insert_payment.php",
          type: "POST",
          dataType: "json",
          data: {
            date_paiement: '<?php echo $date_paiement; ?>',
                numero_facture: '<?php echo $numero_facture; ?>',
                montant_paye: '<?php echo $montant_paye; ?>',
                author: '<?php echo $author; ?>',
                email: '<?php echo $email; ?>',
                id_invoice: '<?php echo $id_invoice; ?>',
                filename: '<?php echo $filename; ?>',
          },
          success: function (response) {
            if (response.status === "success") {
              generatePdfData().then((pdfData) => {
                const postData = {
                    pdfData: pdfData,
                    email: '<?php echo $email; ?>',
                    appartement: '<?php echo $appartement; ?>',
                    nom: '<?php echo $nom; ?>',
                    prenom: '<?php echo $prenom; ?>',
                    message: 'encaissement',
                    date_paiement: '<?php echo $date_paiement ?>',
                    montant_paye: '<?php echo $montant_paye; ?>',
                    numero_facture: '<?php echo $numero_facture; ?>',
                };
                sendPdfData(pdfData, postData).then((response) => {
        Swal.fire({
          title: "Paiement Confirmer!",
          icon: "success",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Terminer",
          cancelButtonText: "Nouveau paiement",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "/index.php";
          } else {
            window.location.href = "/pages/ajout_encaissement.php";
          }
        });
      });
    });
            } else {
                // Re-enable the button
        $("#req").prop("disabled", false);
              alert("Error3: " + response.message);
            }
          },
          error: function (xhr, status, error) {
            alert("Error4: " + error);
          },
        });
      }
    });
  });
</script>

	 

</head>
<body>

<div class="text-center" style="padding:20px;">
	<input type="button" id="rep" value="Valider" class="btn btn-info btn_print">
</div>
<div class="container_content" id="container_content" >
<div class="container" >
        <div class="row">
            <div class="col">
                <p>Facture :<?php echo $numero_facture?></p>
            </div>
            <div class="col">
                <p>Date : <?php echo $dateTime?> </p>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <u><h1>Réservation : <?php echo $appartement ?></h1></u>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p><u><b>Adresse</b></u>: <?php echo $address ?></u></p>
            </div>
        </div>
        <h2 class="mt-4">Coordonnées du client </h2>
        <div class="row">
            <div class="col">
                <p>Nom: <?php echo $nom ?> </p>
            </div>
            <div class="col">
                <p>Prénom: <?php echo $prenom ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>Numéro: <?php echo $telephone ?></p>
            </div>
            <div class="col">
                <p>Email: <?php echo $email ?></p>
            </div>
        </div>

        <h2 class="mt-4">Détails de la Réservation </h2>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Check-In</th>
                    <th scope="col">Check-Out</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Date</td>
                    <td><?php echo $date_entreeF ?></td>
                    <td><?php echo $date_sortieF ?></td>
                </tr>
            </tbody>
        </table>
        <table class="table  mt-3">
            <thead>
                <tr>
                    <th scope="col">Nombre de Nuits</th>
                    <th scope="col">Prix Unitaire</th>
                    <th scope="col">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $nombre_nuits ?></td>
                    <td><?php echo $formatted_tarif_nuitee ?> Fcfa</td>
                    <td><?php echo number_format($montantBeDicount , 0, '', ' ')?> Fcfa</td>
                </tr>
                <?php if ($discount > 0 ){ ?>
                  <td>Discount</td>
                    <td></td>
                    <td>-<?php echo number_format($discount, 0, '', ' ') ?> Fcfa</td>
                  <?php
                } ?>
                <tr>
                    <td>Caution</td>
                    <td></td>
                    <td><?php echo $formatted_caution ?> Fcfa</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end"><strong>Total:</strong></td>
                    <td><?php echo $formatted_toPay ?> Fcfa</td>
                </tr>
            </tbody>
        </table>

        <table class="table  mt-3">
            <tbody>
                <?php echo $table_content ?>
                
                
                
                <tr>
                   <strong><td>Solde restant:</td></strong>
                    <td><?php echo $formatted_balance ?> Fcfa</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <div class="row">
        <div class="col">
            <h2>Check-In : 14h | Check-Out : 12h</h2>
            <p>NB: Un paiement <b>minimum de 50% du total </b>est requis pour la confirmation de la réservation.</p>
        </div>
</div>
<div class="row">
    <div class="col">
        <p>Contact :</p>
    </div>
</div>
<div class="row">
    <div class="col">
        <p>Sur place/WhatsApp : <a href="tel:002250708168284" target="_blank">+225 070 816 8284</a> ——— Seulement WhatsApp : <a href="tel:002250701282929" target="_blank">+225 070 128 2929</a></p>
    </div>
</div>
<div class="row">
    <div class="col">
        <p>E-mail : <a href="mailto:info@gbablé.com" target="_blank">info@gbablé.com</a></p>
    </div>
</div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php } ?>