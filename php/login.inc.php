<?php
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");

// Récupération des données du formulaire de connexion
$email = $_POST['email'];
$mot_de_passe = $_POST['pwd'];

// Récupération de l'utilisateur correspondant à l'adresse email
$sql = "SELECT * FROM users WHERE email = :email";
$requete = $connexion->prepare($sql);
$requete->bindParam(':email', $email);
$requete->execute();

if ($requete->rowCount() == 0) {
    die("Adresse email incorrecte.");
}

$utilisateur = $requete->fetch();

// Vérification du mot de passe
if (!password_verify($mot_de_passe, $utilisateur['pwd'])) {
    die("Mot de passe incorrect.");
}

// Connexion réussie, vous pouvez enregistrer des informations sur l'utilisateur dans la session
session_start();
$_SESSION['user_id'] = $utilisateur['id'];
$_SESSION['user_firstname'] = $utilisateur['first_name'];
$_SESSION['user_lastname'] = $utilisateur['last_name'];
$_SESSION['user_email'] = $utilisateur['email'];

// Convert the comma-separated list of roles into an array
$userRoles = explode(',', $utilisateur['role']);
$_SESSION['user_roles'] = $userRoles;


// Redirection vers la page d'accueil
header('Location: /index.php');
exit();

// Fermeture de la connexion à la base de données
$connexion = null;

?>
