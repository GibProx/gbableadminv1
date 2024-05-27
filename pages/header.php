<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/index.php">Accueil</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <?php if (in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles'])) { ?>
                            <a class="dropdown-item" href="/pages/ajout_depense.php">Ajouter une dépense</a>
                        <?php }
                        if (in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles'])|| in_array('booking', $_SESSION['user_roles'])) { ?>
                            <a class="dropdown-item" href="/pages/ajout_encaissement.php">Ajouter un paiement</a>
                        <?php }
                        if (in_array('admin', $_SESSION['user_roles']) || in_array('booking', $_SESSION['user_roles'])) { ?>
                            <a class="dropdown-item" href="/pages/new_Fct.php">Créer une nouvelle facture</a>
                        <?php }
                        if (in_array('admin', $_SESSION['user_roles']) || in_array('compta', $_SESSION['user_roles'])) { ?>
                            <a class="dropdown-item" href="/pages/suivie.php">Voir le suivi</a>
                        <?php } ?>
                        <?php 
                        if (in_array('admin', $_SESSION['user_roles']) ) { ?>
                            <a class="dropdown-item" href="/pages/signUp.php">Add New User</a>
                        <?php } ?>
                        
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">À propos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
                
            </ul>
            <ul class="navbar-nav ml-auto">
               
                <li class="nav-item">
                    <form action="/php/logout.php" method="post">
                        <button class="btn btn-danger" type="submit">Déconnexion</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</header>