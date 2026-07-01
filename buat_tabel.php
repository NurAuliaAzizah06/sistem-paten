<?php
// 1. Koneksi ke Database
require_once 'koneksi.php';

$sql_tabel_baru = "CREATE TABLE IF NOT EXISTS pemberkasan_ktp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(50) NOT NULL,
    syarat_kk ENUM('Lengkap', 'Belum Lengkap') DEFAULT 'Belum Lengkap',
    syarat_akta ENUM('Lengkap', 'Belum Lengkap') DEFAULT 'Belum Lengkap',
    syarat_surat_pengantar ENUM('Lengkap', 'Belum Lengkap') DEFAULT 'Belum Lengkap',
    file_kk VARCHAR(255) DEFAULT '',
    file_surat_pengantar VARCHAR(255) DEFAULT '',
    status_berkas ENUM('Belum Lengkap', 'Menunggu Verifikasi', 'Lengkap (Sesuai Syarat)') DEFAULT 'Belum Lengkap',
    waktu_verifikasi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nik) REFERENCES warga(nik) ON DELETE CASCADE ON UPDATE CASCADE
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