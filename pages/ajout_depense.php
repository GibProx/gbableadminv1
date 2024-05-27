<?php 
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

// Vérifiez si l'utilisateur a le bon rôle pour accéder à cette page
 // Définissez les rôles autorisés pour cette page
if (!(in_array('admin', $_SESSION['user_roles']) || in_array('service', $_SESSION['user_roles']))) {
    header('Location: /pages/access_denied.php'); // Redirigez vers une page d'accès refusé
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ajouter une nouvelle dépense | GBABLÉ</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Add the SweetAlert2 library -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" ></script>

    <script>
$(document).ready(function() {
  $("#invoice-form").on("submit", function(event) {
    event.preventDefault();
    
    var formData = new FormData(this);

      // Disable the submit button
    var submitButton = $(this).find("button[type='submit']");
    submitButton.prop("disabled", true);

    $.ajax({
      url: "/php/processDepense.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        console.log(response);
        // Handle the response from the server (e.g., show a success message or redirect to another page)
    
        Swal.fire({
          title: "Dépense Ajouter!",
          
          icon: "success",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Terminer",
          cancelButtonText: "Nouvelle Dépense",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "/index.php";
          } else {
            window.location.href = "/pages/ajout_depense.php";
          }
        });
    },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR, textStatus, errorThrown);
        // Handle errors (e.g., show an error message)
        // Enable the submit button
        submitButton.prop("disabled", false);
      }
    });
  });
});
</script>
</head>
<body>

<?php include('/home2/babimors/gbable.motorsfeere.com/pages/header.php') ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header text-center">
                    <h1>Nouvelle dépense</h1>
                </div>
                <div class="card-body">
                    <form  method="post" id="invoice-form">
                        <div class="form-group">
                            <label for="date_paiement">Date de paiement</label>
                            <input type="date" class="form-control" placeholder="Entrez la date de paiement" name="date_paiement" required>
                        </div>
                        <div class="form-group">
                            <label for="date_justificatif">Date de justificatif</label>
                            <input type="date" class="form-control" placeholder="Entrez la date de justificatif" name="date_justificatif" required>
                        </div>
                        <div class="form-group">
                            <label for="designation">Désignation</label>
                            <input type="text" class="form-control" placeholder="Entrez la désignation" name="designation" required>
                        </div>
                        <div class="form-group">
                            <label for="montant">Montant</label>
                            <input type="number" class="form-control" placeholder="Entrez le montant" name="montant" inputmode="numeric" required>
                        </div>
                        <div class="form-group">
                            <label>Appartement concerné</label>
                            <select class="form-control" name="appartement" required>
                                <option value="" disabled selected>Choisir un appartement</option>
                                <option value="A4">A4</option>
                                <option value="A5">A5</option>
                                <option value="A6">A6</option>
                                <option value="A8">A8</option>
                                <option value="Spaceroom">Spaceroom</option>
                                <option value="Centrum">Centrum</option>
                                <option value="Bliss1">BLISS I</option>
                                <option value="Oasis1">OASIS I</option>
                                <option value="Vernoise1">Vernoise I</option>
                                <option value="Vernoise2">Vernoise II</option>
                                <option value="Lacotiere1">La Côtière I</option>
                                <option value="autre">Autre</option>
                                <option value="tmtm">Transfert Membre à Membre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control-file" name="image" accept="image/*" required>
                        </div>
                        <button type="submit"  class="btn btn-primary">Ajouter la facture</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
