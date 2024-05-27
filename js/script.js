function ouvrirOnglet(evt, nomOnglet) {
    // Déclaration des variables
    var i, tabContent, tabLinks;

    // Récupération de tous les éléments avec la classe "tab-content" et masquage de leur contenu
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    }

    // Récupération de tous les boutons avec la classe "tablinks" et suppression de la classe "active"
    tabLinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tabLinks.length; i++) {
        tabLinks[i].className = tabLinks[i].className.replace(" active", "");
    }

    // Affichage du contenu de l'onglet sélectionné et ajout de la classe "active" au bouton correspondant
    document.getElementById(nomOnglet).style.display = "block";
    evt.currentTarget.className += " active";
}

// Définition de l'onglet actif par défaut
document.getElementsByClassName("tablinks")[0].click();