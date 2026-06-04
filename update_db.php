<?php
// Koneksi ke database (menggunakan password yang sudah sinkron kemarin)
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');

// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Menambahkan kolom status_berkas ke tabel warga
$sql = "ALTER TABLE warga ADD COLUMN status_berkas VARCHAR(50) DEFAULT 'Belum Lengkap'";

if ($conn->query($sql) === TRUE) {
    echo "✅ MANTAP! Kolom Status Berkas KTP berhasil ditambahkan!";
} else {
    echo "Gagal atau kolom sudah pernah ditambahkan: " . $conn->error;
}

$conn->close();
?>