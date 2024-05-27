<?php 
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");
// Récupération des données du formulaire d'inscription
$nom = $_POST['lastname'];
$prenom = $_POST['firstname'];
$email = $_POST['email'];
$mot_de_passe = $_POST['pwd'];
$confirm_mot_de_passe = $_POST['pwdConfirmed'];

// Vérification que les deux mots de passe sont identiques
if ($mot_de_passe != $confirm_mot_de_passe) {
    die("Les mots de passe ne correspondent pas.");
} else {
    $password_hash = password_hash($confirm_mot_de_passe, PASSWORD_DEFAULT);
}

// Vérification si l'utilisateur existe déjà dans la base de données
$sql = "SELECT * FROM users WHERE email = :email";
$requete = $connexion->prepare($sql);
$requete->bindParam(':email', $email);
$requete->execute();

if ($requete->rowCount() > 0) {
    die("Un utilisateur avec cet email existe déjà.");
}

// Insertion des données de l'utilisateur dans la base de données
$sql = "INSERT INTO users (last_name, first_name, email, pwd) VALUES (:nom, :prenom, :email, :mot_de_passe)";
$requete = $connexion->prepare($sql);
$requete->bindParam(':nom', $nom);
$requete->bindParam(':prenom', $prenom);
$requete->bindParam(':email', $email);
$requete->bindParam(':mot_de_passe', $password_hash);

if ($requete->execute()) {
    echo "Nouvel utilisateur inséré avec succès.";
} else {
    echo "Erreur : " . $sql . "<br>" . $requete->errorInfo();
}

// Fermeture de la connexion à la base de données
$connexion = null;
?>