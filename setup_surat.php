<?php
// Koneksi ke database
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');

// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Perintah untuk membuat tabel surat
$sql = "CREATE TABLE IF NOT EXISTS surat (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(50) NOT NULL,
    nama_warga VARCHAR(100) NOT NULL,
    jenis_surat VARCHAR(50) NOT NULL,
    tanggal_buat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ MANTAP! Tabel Surat berhasil dibuat dan siap digunakan!";
} else {
    echo "Gagal membuat tabel: " . $conn->error;
}

$conn->close();
?>