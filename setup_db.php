<?php
$conn = new mysqli('db', 'root', 'password_paten', 'db_paten');

$sql = "CREATE TABLE IF NOT EXISTS warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    tgl_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ Tabel Warga siap digunakan!";
} else {
    echo "❌ Gagal membuat tabel: " . $conn->error;
}
?>