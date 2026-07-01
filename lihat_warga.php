<?php
session_start();
// Gembok Keamanan
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
require_once 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Warga - PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-header { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0">Daftar Warga Kecamatan Birayang</h5>
                <div>
                    <a href="index.php" class="btn btn-sm btn-outline-light me-1">Beranda</a>
                    <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </div>

            <div class="card-body p-4">
                <form method="GET" action="" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" name="cari" class="form-control" placeholder="Cari NIK atau Nama warga..." value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                                <button class="btn btn-primary" type="submit">Cari</button>
                                <?php if(isset($_GET['cari']) && trim($_GET['cari']) != ''): ?>
                                    <a href="lihat_warga.php" class="btn btn-outline-secondary">Reset</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a href="cetak_surat.php" target="_blank" class="btn btn-success w-100">
                                🖨️ Cetak Laporan
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">NO</th>
                                <th width="15%">NIK</th>
                                <th width="30%">NAMA</th>
                                <th width="15%">TANGGAL INPUT</th>
                                <th width="20%">STATUS BERKAS</th> 
                                <th width="15%" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php
    // LOGIKA PENCARIAN 
    if (isset($_GET['cari']) && trim($_GET['cari']) != '') {
        $cari_teks = "%" . trim($_GET['cari']) . "%";
        $stmt = $conn->prepare("SELECT * FROM warga WHERE nik LIKE ? OR nama LIKE ? ORDER BY waktu_input DESC");
        $stmt->bind_param("ss", $cari_teks, $cari_teks);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM warga ORDER BY waktu_input DESC");
    }

    // TAMPILKAN DATA
    if ($result && $result->num_rows > 0) {
        $no = 1; // Inisialisasi angka awal untuk penomoran
        
        while($row = $result->fetch_assoc()):
            // Meracik format tanggal agar hanya menampilkan Hari-Bulan-Tahun
            $tanggal_saja = date('d-m-Y', strtotime($row['waktu_input']));
    ?>
    <tr>
        <td class="text-center"><?php echo $no++; ?></td>
        <td><?php echo htmlspecialchars($row['nik']); ?></td>
        <td><?php echo htmlspecialchars($row['nama']); ?></td>
        
        <td><?php echo $tanggal_saja; ?></td>
        
        <td>
            <?php 
                $status = $row['status_berkas'];
                if($status == 'Lengkap (Sesuai Syarat)') {
                    echo "<span class='badge bg-success'>$status</span>";
                } elseif($status == 'Menunggu Verifikasi') {
                    echo "<span class='badge bg-warning text-dark'>$status</span>";
                } else {
                    echo "<span class='badge bg-danger'>$status</span>";
                }
            ?>
        </td>
        <td class="text-center">
            <a href="edit_warga.php?nik=<?php echo $row['nik']; ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="hapus_warga.php?nik=<?php echo $row['nik']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
        </td>
    </tr>
    <?php 
        endwhile; 
    } else {
        // Sesuaikan colspan menjadi 6 karena sekarang ada tambahan kolom No.
        echo "<tr><td colspan='6' class='text-center text-muted'>Data tidak ditemukan.</td></tr>";
    }
    $conn->close();
    ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>