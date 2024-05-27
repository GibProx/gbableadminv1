<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Property</title>
</head>
<body>
    <h1>Add New Property</h1>
    <form action="/php/add_property.inc.php" method="POST">
        <label for="name">Property Name:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="owner">Owner Name:</label>
        <input type="text" id="owner" name="owner" required>
        <br>
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required>
        <br>
        <label for="night_price">Night Price:</label>
        <input type="number" id="night_price" name="night_price" step="0.01" min="0" required>
        <br>
        <label for="night_price">Caution:</label>
        <input type="number" id="caution" name="caution" step="0.01" min="0" required>
        <br>
        
        <button type="submit">Add Property</button>
    </form>
</body>
</html>