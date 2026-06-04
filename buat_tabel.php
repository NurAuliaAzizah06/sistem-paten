<?php
// 1. Koneksi ke Database db_paten kamu
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');

// Cek koneksi
if ($conn->connect_error) {
    die("<h3 style='color:red;'>Koneksi ke database gagal: " . $conn->connect_error . "</h3>");
}

// Langkah A: Buat tabel baru dengan tipe data NIK VARCHAR yang fleksibel tanpa kekakuan constraint
$sql_tabel_baru = "CREATE TABLE IF NOT EXISTS pemberkasan_ktp (
    id_pemberkasan INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(50) NOT NULL,
    syarat_kk ENUM('Lengkap', 'Belum Lengkap') DEFAULT 'Belum Lengkap',
    syarat_akta ENUM('Lengkap', 'Belum Lengkap') DEFAULT 'Belum Lengkap',
    syarat_surat_pengantar ENUM('Lengkap', 'Belum Lengkap') DEFAULT 'Belum Lengkap',
    status_berkas ENUM('Belum Lengkap', 'Menunggu Verifikasi', 'Lengkap (Sesuai Syarat)') DEFAULT 'Belum Lengkap',
    tgl_pemberkasan TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// 2. Eksekusi pembuatan tabel baru
if ($conn->query($sql_tabel_baru) === TRUE) {
    echo "<h2 style='color:green; font-family:sans-serif;'>🎉 HORE! Tabel 'pemberkasan_ktp' SUDAH BERHASIL 100% DIBUAT!</h2>";
    echo "<p>Sistem database sudah siap digunakan tanpa kendala relasi kaku. Lia sekarang resmi bisa lanjut ke tahap berikutnya!</p>";
} else {
    echo "<h2 style='color:red; font-family:sans-serif;'>❌ Gagal membuat tabel: " . $conn->error . "</h2>";
}

$conn->close();
?>