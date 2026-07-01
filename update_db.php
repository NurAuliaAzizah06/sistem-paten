<?php
// Koneksi ke database
require_once 'koneksi.php';

// Menambahkan kolom status_berkas ke tabel warga
$sql = "ALTER TABLE warga ADD COLUMN status_berkas VARCHAR(50) DEFAULT 'Belum Lengkap'";

if ($conn->query($sql) === TRUE) {
    echo "✅ MANTAP! Kolom Status Berkas KTP berhasil ditambahkan!";
} else {
    echo "Gagal atau kolom sudah pernah ditambahkan: " . $conn->error;
}

$conn->close();
?>