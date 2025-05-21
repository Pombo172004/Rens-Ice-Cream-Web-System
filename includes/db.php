<?php

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname_rctech = "makmak1";

// Create a PDO connection for the 'rctech' database
try {
    $pdo_makmak1 = new PDO("mysql:host=$servername;dbname=$dbname_rctech", $username, $password);
    $pdo_makmak1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Connected to makmak1 database successfully!";
} catch (PDOException $e) {
    die("❌ makmak1 Database Connection Failed: " . $e->getMessage());
}


// // Create a PDO connection for the 'inventory_db' database
// try {
//     $pdo_inventory = new PDO("mysql:host=$servername;dbname=$dbname_inventory", $username, $password);
//     $pdo_inventory->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("❌ Inventory Database Connection Failed: " . $e->getMessage());
// }

    

?>