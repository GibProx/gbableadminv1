<?php
session_start();
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
include("/home2/babimors/gbable.motorsfeere.com/php/functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    $author_id = $_SESSION['user_id'];
    $numero_facture = $_POST['numero_facture'];
    $remaining_balance = $_POST['totalToPay'];
    $filename = $_POST['filename'];
    $discount = $_POST['discount'];
    

    $result = insert_invoice_data($connexion, $appartement, $nom, $prenom, $telephone, $email, $date_entree, $date_sortie, $tarif_nuitee, $nombre_nuits, $total, $filename, $numero_facture, $remaining_balance, $discount, $author_id);

    echo json_encode(array("status" => "success", "message" => "Invoice data saved successfully"));



} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request"));
}
?>
