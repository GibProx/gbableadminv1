<?php 
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function get_property_names($connexion) {
    $sql = "SELECT name FROM property ORDER BY name";
    $stmt = $connexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function get_night_price_by_name($connexion, $apartment_name) {
    $sql = "SELECT night_price FROM property WHERE name = :apartment_name";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':apartment_name', $apartment_name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['night_price'];
    } else {
        return null;
    }
}

function get_caution_by_name($connexion, $apartment_name) {
    $sql = "SELECT caution FROM property WHERE name = :apartment_name";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':apartment_name', $apartment_name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['caution'];
    } else {
        return null;
    }
}

function get_address_by_name($connexion, $apartment_name) {
    $sql = "SELECT address FROM property WHERE name = :apartment_name";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':apartment_name', $apartment_name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['address'];
    } else {
        return null;
    }

}

function get_invoice($connexion) {
    $sql = "SELECT * FROM `invoice` ORDER BY `invoice`.`date_insertion` DESC";
    $stmt = $connexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function check_status_reservation($connexion, $id_reservation) {
    $sql = "SELECT status FROM invoice WHERE id = :id_reservation";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':id_reservation', $id_reservation);
    $stmt->execute();

    $statut = $stmt->fetch(PDO::FETCH_ASSOC);

    echo $statut['status'];
}

function generate_invoice_number($connexion) {
    $sql = "SELECT SUBSTR(invoice_number, 5) AS invoice_order FROM invoice WHERE invoice_number LIKE CONCAT(DATE_FORMAT(CURRENT_DATE, '%y%m'), '%') ORDER BY invoice_order DESC LIMIT 1";
    $stmt = $connexion->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $last_order = intval($result['invoice_order']);
        $new_order = $last_order + 1;
    } else {
        $new_order = 1;
    }

    $invoice_number = date('ym') . str_pad($new_order, 2, '0', STR_PAD_LEFT);

    return $invoice_number;
}



function generateUniqueFilename($folderPath) {
    $filename = null;
    do {
        $randomNumber = mt_rand(100000, 999999);
        $filename = $folderPath . DIRECTORY_SEPARATOR . $randomNumber . '.txt';
        $name = 'PF'. $randomNumber;
    } while (file_exists($filename));

    return $name;
}






function update_invoice_status($connexion, $id_invoice, $new_status, $new_invoice_number) {
    $sql = "UPDATE invoice SET status = :new_status, invoice_number = :new_invoice_number WHERE id = :id_invoice";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':id_invoice', $id_invoice);
    $stmt->bindParam(':new_status', $new_status);
    $stmt->bindParam(':new_invoice_number', $new_invoice_number);

    try {
        $stmt->execute();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

function check_status_invoice($connexion, $id_invoice) {
    $sql = "SELECT status FROM invoice WHERE id = :id_invoice";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':id_invoice', $id_invoice);

    try {
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['status'];
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
function get_invoice_number($connexion, $id_invoice) {
    $sql = "SELECT invoice_number FROM invoice WHERE id = :id_invoice";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':id_invoice', $id_invoice);

    try {
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['invoice_number'];
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
function update_remaining_balance($connexion, $invoice_id, $amount_paid) {
    $sql = "UPDATE invoice SET remaining_balance = remaining_balance - :amount_paid WHERE id = :invoice_id";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':amount_paid', $amount_paid);
    $stmt->bindParam(':invoice_id', $invoice_id);
    $stmt->execute();
}

function display_invoice_information($connexion, $id_invoice) {
    $sql = "SELECT * FROM invoice WHERE id = :id_invoice";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':id_invoice', $id_invoice);
    $stmt->execute();
    
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($invoice) {
        return $invoice;
    } else {
        return false;
    }
}
function get_encaissements_by_invoice($connexion, $invoice_number) {
    $sql = "SELECT * FROM encaissement WHERE invoice_number = :invoice_number";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':invoice_number', $invoice_number);
    $stmt->execute();

    $encaissements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $encaissements;
}

function display_encaissements($encaissements) {
    foreach ($encaissements as $encaissement) {
        echo "<tr>";
        echo "<td class='td16' valign='middle'>";
        echo "<p class='p4'>Montant pay&eacute; :</p>";
        echo "</td>";
        echo "<td class='td17' valign='middle'>";
        echo "<p class='p16'>" . $encaissement['montant_paye'] . "</p>";
        echo "</td>";
        echo "<td class='td18' valign='middle'>";
        echo "<p class='p4'>Fcfa</p>";
        echo "</td>";
        echo "<td class='td19' valign='middle'>";
        echo "<p class='p17'>" . $encaissement['date_paiement'] . "</p>";
        echo "</td>";
        echo "</tr>";
    }
}

function update_nom_facture($connexion, $id_invoice, $new_nom_facture) {
    // Prepare the SQL query to update the 'nom_facture' column in the 'invoice' table
    $sql = "UPDATE invoice SET nom_facture = :new_nom_facture WHERE id = :id_invoice";
    
    try {
        // Prepare the statement
        $stmt = $connexion->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':id_invoice', $id_invoice, PDO::PARAM_INT);
        $stmt->bindParam(':new_nom_facture', $new_nom_facture, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
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

};

function insert_invoice_data($connexion, $appartement, $nom, $prenom, $telephone, $email, $date_entree, $date_sortie, $tarif_nuitee, $nombre_nuits, $total, $filename, $numero_facture, $remaining_balance, $discount, $author_id) {
   // Prepare an INSERT statement
   $stmt = $connexion->prepare("INSERT INTO invoice (invoice_number, appartement, nom, prenom, telephone, email, date_entree, date_sortie, tarif_nuitee, nombre_nuits, total, nom_facture, remaining_balance, discount, author_id) VALUES (:numero_facture, :appartement, :nom, :prenom, :telephone, :email, :date_entree, :date_sortie, :tarif_nuitee, :nombre_nuits, :total, :nom_facture, :remaining_balance, :discount, :author_id)");
   // Bind parameters  
   $stmt->bindParam(':numero_facture', $numero_facture);
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
   $stmt->bindParam(':remaining_balance', $remaining_balance);
   $stmt->bindParam(':discount', $discount);
   $stmt->bindParam(':author_id', $author_id);
   // Execute the statement
   $stmt->execute();

   if ($stmt) {
    return true;
} else {
    return false;
}



};

function insert_payment_data($connexion, $date_paiement, $numero_facture, $montant_paye, $author, $id_invoice, $new_nom_facture) {
    update_invoice_status($connexion, $id_invoice, 'Confirmed', $numero_facture);
    // Prepare an INSERT statement
    $sql = "INSERT INTO encaissement (date_paiement, invoice_number, montant_paye, author) VALUES (:date_paiement, :numero_facture, :montant_paye, :author)";
    // Bind parameters
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':date_paiement', $date_paiement);
    $stmt->bindParam(':numero_facture', $numero_facture);
    $stmt->bindParam(':montant_paye', $montant_paye);
    $stmt->bindParam(':author', $author);
    
    // Execute the statement
    $stmt->execute();
  // Update the remaining balance for the invoice
  update_remaining_balance($connexion, $id_invoice, $montant_paye);
  update_nom_facture($connexion, $id_invoice, $new_nom_facture);

  
  
    if ($stmt) {
     return true;
 } else {
     return false;
 }
 
 
 
 };

 function get_remaining_balance($connexion, $id_invoice) {
    // Query to get the remaining balance for the given invoice
    $sql = "SELECT remaining_balance FROM invoice WHERE id = ?";
    
    // Prepare the query
    $stmt = $connexion->prepare($sql);

    // Bind the parameter and execute the query
    $stmt->bindParam(1, $id_invoice, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if there is a record for the given invoice
    if ($result !== false) {
        // Get the remaining balance
        $remaining_balance = $result['remaining_balance'];
      
        return $remaining_balance;
    } else {
        return false;
    }
}
