<?php
// db.php - Database Connection File

$servername = "localhost"; // Assuming localhost, change if needed
$username = "uasxxqbztmxwm";
$password = "wss863wqyhal";
$dbname = "dbeugyx5gjlwtl";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // Uncomment for testing
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
