<?php
// Database configuration
include("/home2/babimors/gbable.motorsfeere.com/php/db_connect.php");

try {
   

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $name = $_POST['name'];
        $owner = $_POST['owner'];
        $address = $_POST['address'];
        $night_price = $_POST['night_price'];
        $caution = $_POST['caution'];

        // Prepare the SQL query
        $sql = "INSERT INTO property (name, owner, address, night_price, caution) VALUES (:name, :owner, :address, :night_price, :caution)";
        $stmt = $connexion->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':owner', $owner);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':night_price', $night_price);
        $stmt->bindParam(':caution', $caution);

        // Execute the query
        try {
            $stmt->execute();
            // Show a success message
            echo "New property added successfully!";
        } catch (PDOException $e) {
            // Check for duplicate name error (MySQL error code 1062)
            if ($e->errorInfo[1] == 1062) {
                echo "Error: A property with this name already exists.";
            } else {
                // Show other error messages
                echo "Error: " . $e->getMessage();
            }
        }
    }
} catch(PDOException $e) {
    // Show error message
    echo "Error: " . $e->getMessage();
}

// Close the PDO connection
$connexion = null;
