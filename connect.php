<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = '131.153.147.98';
$database = 'campusa6_php_zuri';
$username = 'campusa6_zuri_student';
$password = 'MtD3^fpOQ$z9';

$connectionStatus = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check the connection status
    if ($pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
        $connectionStatus = "Connected successfully";
    } else {
        $connectionStatus = "Failed to connect";
    }
} catch(PDOException $e) {
    $connectionStatus = "Connection failed: " . $e->getMessage();
}

// Return connection status
return $connectionStatus;
?>
