<?php
session_start();
// Gembok Keamanan
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
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
                                <input type="text" name="cari" class="form-control" placeholder="Cari NIK atau Nama warga..." value="<?php echo isset($_GET['cari']) ? $_GET['cari'] : ''; ?>">
                                <button class="btn btn-primary" type="submit">Cari</button>
                                <?php if(isset($_GET['cari'])): ?>
                                    <a href="lihat_warga.php" class="btn btn-outline-secondary">Reset</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a href="cetak_warga.php" target="_blank" class="btn btn-success w-100">
                                🖨️ Cetak Laporan
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="20%">NIK</th>
                                <th width="40%">Nama</th>
                                <th width="20%">Waktu Input</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php
    $conn = new mysqli('db', 'root', 'password_paten', 'db_paten');
    
    // LOGIKA PENCARIAN (Pindahkan ke sini biar sinkron sama tabel)
    if (isset($_GET['cari']) && $_GET['cari'] != '') {
        $cari_teks = "%" . $_GET['cari'] . "%";
        $stmt = $conn->prepare("SELECT * FROM warga WHERE nik LIKE ? OR nama LIKE ? ORDER BY tgl_input DESC");
        $stmt->bind_param("ss", $cari_teks, $cari_teks);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM warga ORDER BY tgl_input DESC");
    }

    // TAMPILKAN DATA
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()): 
    ?>
    <tr>
        <td><?php echo $row['nik']; ?></td>
        <td><?php echo $row['nama']; ?></td>
        <td><?php echo $row['tgl_input']; ?></td>
        <td class="text-center">
            <a href="edit_warga.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="hapus_warga.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</a>
        </td>
    </tr>
    <?php 
        endwhile; 
    } else {
        echo "<tr><td colspan='4' class='text-center text-muted'>Data tidak ditemukan.</td></tr>";
    }
    ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>