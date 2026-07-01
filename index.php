<?php
// Uji coba integrasi pipeline CI/CD Sistem PATEN
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 1. Koneksi Database
require_once 'koneksi.php';

// Set nilai default awal menjadi 0
$total_warga = 0;
$total_lengkap = 0;
$total_menunggu = 0;

// 2. Ambil Total Warga
$sql_warga = $conn->query("SELECT COUNT(*) as total FROM warga");
if ($sql_warga) {
    $data_warga = $sql_warga->fetch_assoc();
    $total_warga = $data_warga['total'];
}

// 3. Ambil Total Berkas Lengkap/Selesai (KTP)
$sql_lengkap = $conn->query("SELECT COUNT(*) as total FROM pemberkasan_ktp WHERE status_berkas LIKE '%Lengkap%'");
if ($sql_lengkap) {
    $data_lengkap = $sql_lengkap->fetch_assoc();
    $total_lengkap = $data_lengkap['total'];
}

// 4. Ambil Total Berkas Menunggu Verifikasi (KTP)
$sql_menunggu = $conn->query("SELECT COUNT(*) as total FROM pemberkasan_ktp WHERE status_berkas = 'Menunggu Verifikasi'");
if ($sql_menunggu) {
    $data_menunggu = $sql_menunggu->fetch_assoc();
    $total_menunggu = $data_menunggu['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard PATEN Birayang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">SISTEM PATEN BIRAYANG</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card border-0 shadow-sm p-4">
                    <h3 class="mb-1">Halo, <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong>! 👋</h3>
                    <p class="text-muted mb-0">SELAMAT DATANG</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="d-flex flex-column gap-3 h-100">
                    <div class="card bg-primary text-white text-center p-3 shadow-sm flex-fill d-flex flex-column justify-content-center">
                        <h6 class="fw-bold mb-1">TOTAL WARGA TERDAFTAR</h6>
                        <h1 class="display-5 fw-bold mb-0"><?php echo $total_warga; ?></h1>
                    </div>
                    
                    <div class="card bg-success text-white text-center p-3 shadow-sm flex-fill d-flex flex-column justify-content-center">
                        <h6 class="fw-bold mb-1">BERKAS SELESAI (LENGKAP)</h6>
                        <h1 class="display-5 fw-bold mb-0"><?php echo $total_lengkap; ?></h1>
                    </div>

                    <div class="card bg-warning text-dark text-center p-3 shadow-sm flex-fill d-flex flex-column justify-content-center">
                        <h6 class="fw-bold mb-1">MENUNGGU VERIFIKASI</h6>
                        <h1 class="display-5 fw-bold mb-0"><?php echo $total_menunggu; ?></h1>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="fw-bold mb-4">Menu Pelayanan</h5>
                    
                    <div class="d-flex gap-3 flex-wrap align-items-stretch">
                        <a href="tambah_warga.php" class="btn btn-success btn-lg flex-fill py-4 d-flex flex-column justify-content-center align-items-center" style="min-width: 150px;">
                            <i class="bi bi-person-plus-fill mb-2" style="font-size: 2rem;"></i>
                            Tambah Warga
                        </a>
                        <a href="lihat_warga.php" class="btn btn-outline-primary btn-lg flex-fill py-4 d-flex flex-column justify-content-center align-items-center" style="min-width: 150px;">
                            <i class="bi bi-people-fill mb-2" style="font-size: 2rem;"></i>
                            Daftar Warga
                        </a>
                        <a href="pemberkasan.php" class="btn btn-primary btn-lg flex-fill py-4 d-flex flex-column justify-content-center align-items-center" style="min-width: 150px;">
                            <i class="bi bi-file-earmark-text-fill mb-2" style="font-size: 2rem;"></i>
                            Pemberkasan KTP
                        </a>
                        <a href="dispensasi_nikah.php" class="btn btn-danger btn-lg flex-fill py-4 d-flex flex-column justify-content-center align-items-center" style="min-width: 150px;">
                            <i class="bi bi-people-fill mb-2" style="font-size: 2rem;"></i>
                            Dispensasi Nikah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>