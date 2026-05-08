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
                <form action="simpan_warga.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control" placeholder="Masukkan 16 digit NIK" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama sesuai KTP" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan Data</button>
                    <a href="index.php" class="btn btn-link w-100 mt-2 text-decoration-none text-muted">Kembali ke Beranda</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>