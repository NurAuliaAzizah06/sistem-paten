<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Warga - PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow mx-auto" style="max-width: 500px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Data Warga Birayang</h5>
            </div>
            <div class="card-body">
                <form action="simpan_warga.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control" placeholder="Masukkan 16 digit NIK" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama sesuai KTP" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Surat Pengantar dari RT/RW</label>
                        <input type="file" name="file_surat_pengantar" class="form-control" accept="image/*,.pdf" required>
                        <div class="form-text">Format yang diizinkan: JPG, PNG, atau PDF.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kartu Keluarga (Fotokopi/Scan)</label>
                        <input type="file" name="file_kk" class="form-control" accept="image/*,.pdf" required>
                        <div class="form-text">Format yang diizinkan: JPG, PNG, atau PDF.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Dokumen KTP</label>
                        <select name="status_berkas" class="form-control" required>
                            <option value="Belum Lengkap">Belum Lengkap</option>
                            <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                            <option value="Lengkap (Sesuai Syarat)">Lengkap (Sesuai Syarat)</option>
                        </select>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success w-100 mb-2">Simpan Data</button>
                        <a href="lihat_warga.php" class="btn btn-outline-secondary w-100">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>