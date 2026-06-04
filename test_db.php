<?php
$host = 'db'; // Nama service di docker-compose
$user = 'root';
$pass = 'bismillah123'; // Sesuai password di docker-compose
$db   = 'db_paten';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Aduh, koneksi gagal: " . $conn->connect_error);
} 
echo "✅ MANTAP! PHP Berhasil Konek ke Database PATEN!";
?>