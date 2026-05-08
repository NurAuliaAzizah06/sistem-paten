<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 1. Koneksi Database
$conn = new mysqli('db', 'root', 'password_paten', 'db_paten');

// 2. Ambil Total Warga
$sql_warga = $conn->query("SELECT COUNT(*) as total FROM warga");
$data_warga = $sql_warga->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem PATEN Birayang</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card border-0 shadow-sm p-3">
                    <h3>Halo, <strong><?php echo $_SESSION['admin']; ?></strong>! 👋</h3>
                    <p class="text-muted">Selamat mengelola data kependudukan hari ini.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm bg-primary text-white p-4 text-center">
                    <h6 class="text-uppercase small">Total Warga Terdaftar</h6>
                    <h1 class="display-4 fw-bold"><?php echo $data_warga['total']; ?></h1>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm p-4" style="height: 100%;">
                    <h5 class="fw-bold mb-3">Menu Utama</h5>
                    <div class="d-flex gap-3">
                        <a href="tambah_warga.php" class="btn btn-success btn-lg flex-fill py-3">
                            ➕ Tambah Warga
                        </a>
                        <a href="lihat_warga.php" class="btn btn-outline-primary btn-lg flex-fill py-3">
                            📋 Daftar Warga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>