<?php
$host = 'localhost';
$dbname = 'event_reservations';
$username = 'root';
$password = ''; // Cambia si tienes contraseña en MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>