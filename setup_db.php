<?php
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Perintah untuk membuat tabel warga yang sinkron
$sql = "CREATE TABLE IF NOT EXISTS warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nik VARCHAR(16) NOT NULL,
    alamat TEXT
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ MANTAP! Tabel Warga sudah sinkron sekarang!";
} else {
    echo "Gagal: " . $conn->error;
}

$conn->close();
?>