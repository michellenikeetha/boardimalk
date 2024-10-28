<?php
$host = 'sql109.infinityfree.com';
$dbname = 'if0_37602148_bodimbuddy';
$username = 'if0_37602148';
$password = 'l8Rk9mcrNo';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>
