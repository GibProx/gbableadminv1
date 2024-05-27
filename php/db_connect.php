<?php

// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "babimors_adminGbable";
$mot_de_passe = "BSgQqBTzo(c5";
$base_de_donnees = "babimors_gbable";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$base_de_donnees", $utilisateur, $mot_de_passe);
    // Configuration de PDO pour afficher les erreurs
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // var_dump("COnnected");
} catch(PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}
?>