<?php
require_once 'koneksi.php';

$errors = [];
$success = [];

// Drop tabel yang salah skema (data masih kosong, admin sudah dibuat sendiri)
$conn->query("DROP TABLE IF EXISTS dispensasi_nikah");
$conn->query("DROP TABLE IF EXISTS pemberkasan_ktp");
$conn->query("DROP TABLE IF EXISTS warga");

// Tabel warga
$sql = "CREATE TABLE warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    status_berkas ENUM('Belum Lengkap','Menunggu Verifikasi','Lengkap (Sesuai Syarat)') DEFAULT 'Belum Lengkap',
    waktu_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql)) {
    $success[] = "Tabel <strong>warga</strong> berhasil dibuat.";
} else {
    $errors[] = "Gagal buat tabel warga: " . $conn->error;
}

// Tabel pemberkasan_ktp
$sql = "CREATE TABLE pemberkasan_ktp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(50) NOT NULL,
    syarat_kk ENUM('Lengkap','Belum Lengkap') DEFAULT 'Belum Lengkap',
    syarat_akta ENUM('Lengkap','Belum Lengkap') DEFAULT 'Belum Lengkap',
    syarat_surat_pengantar ENUM('Lengkap','Belum Lengkap') DEFAULT 'Belum Lengkap',
    file_kk VARCHAR(255) DEFAULT '',
    file_surat_pengantar VARCHAR(255) DEFAULT '',
    status_berkas ENUM('Belum Lengkap','Menunggu Verifikasi','Lengkap (Sesuai Syarat)') DEFAULT 'Belum Lengkap',
    waktu_verifikasi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nik) REFERENCES warga(nik) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($sql)) {
    $success[] = "Tabel <strong>pemberkasan_ktp</strong> berhasil dibuat.";
} else {
    $errors[] = "Gagal buat tabel pemberkasan_ktp: " . $conn->error;
}

// Tabel dispensasi_nikah
$sql = "CREATE TABLE dispensasi_nikah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(50) NOT NULL,
    nomor_surat VARCHAR(100),
    tanggal_pengajuan DATE,
    status ENUM('Menunggu Verifikasi','Lengkap (Sesuai Syarat)','Ditolak / Tidak Memenuhi') DEFAULT 'Menunggu Verifikasi',
    surat_pengantar ENUM('Ada','Tidak') DEFAULT 'Tidak',
    blangko_n1_n7 ENUM('Ada','Tidak') DEFAULT 'Tidak',
    surat_pindah_nikah ENUM('Ada','Tidak') DEFAULT 'Tidak',
    pas_foto ENUM('Ada','Tidak') DEFAULT 'Tidak',
    fotokopi_akta_lahir_dan_ijazah_terakhir ENUM('Ada','Tidak') DEFAULT 'Tidak',
    FOREIGN KEY (nik) REFERENCES warga(nik) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($sql)) {
    $success[] = "Tabel <strong>dispensasi_nikah</strong> berhasil dibuat.";
} else {
    $errors[] = "Gagal buat tabel dispensasi_nikah: " . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setup Database - PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Setup Database PATEN</h5>
        </div>
        <div class="card-body p-4">
            <?php foreach ($success as $s): ?>
                <div class="alert alert-success">✅ <?= $s ?></div>
            <?php endforeach; ?>
            <?php foreach ($errors as $e): ?>
                <div class="alert alert-danger">❌ <?= $e ?></div>
            <?php endforeach; ?>
            <?php if (empty($errors)): ?>
                <div class="alert alert-info mt-3">
                    <strong>Semua tabel siap!</strong> Silakan kembali ke aplikasi.
                </div>
                <a href="index.php" class="btn btn-primary w-100">Ke Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
