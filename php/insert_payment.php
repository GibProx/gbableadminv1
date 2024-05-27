<?php
session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_paiement = $_POST['date_paiement'];
    $numero_facture = $_POST['numero_facture'];
    $montant_paye = $_POST['montant_paye'];
    $author = $_POST['author'];
    $email = $_POST['email'];
    $id_invoice = $_POST['id_invoice'];
    $author = $_SESSION['user_firstname'];
    $filename = $_POST['filename'];
    

    $result = insert_payment_data($connexion, $date_paiement, $numero_facture, $montant_paye, $author, $id_invoice, $filename);

    if ($result) {
        // Get the remaining balance after payment
        $remaining_balance = get_remaining_balance($connexion, $id_invoice);

        // Check if the remaining balance is less than or equal to 0
        if ($remaining_balance <= 0) {
            // Update the invoice status to 'Paid'
            update_invoice_status($connexion, $id_invoice, 'Paid', $numero_facture);
        }

        echo json_encode(array("status" => "success", "message" => "Invoice data saved successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error saving invoice data"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request"));
}
?>