<?php
// 1. Proteksi Session Keamanan Area Admin
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 2. Koneksi database Docker kamu
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');
if ($conn->connect_error) { 
    die("Koneksi gagal: " . $conn->connect_error); 
}

// 3. Ambil data lama yang mau diedit berdasarkan ID di URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Query disesuaikan menggunakan p.id sesuai struktur tabel baru
    $query = $conn->query("SELECT p.*, w.nama FROM pemberkasan_ktp p 
                           JOIN warga w ON p.nik = w.nik 
                           WHERE p.id = $id");
    $data = $query->fetch_assoc();
    
    if (!$data) {
        die("<div class='container mt-5'><div class='alert alert-danger'>Data tidak ditemukan!</div></div>");
    }
} else {
    header("Location: pemberkasan.php");
    exit;
}

// 4. Proses update data saat tombol "Simpan Perubahan" diklik
if (isset($_POST['update'])) {
    // Pengamanan data input dari form
    $file_kk = $conn->real_escape_string($_POST['file_kk']);
    $file_surat_pengantar = $conn->real_escape_string($_POST['file_surat_pengantar']);
    
    // Logika penentuan status kelayakan berkas secara otomatis
    if ($file_kk == 'Lengkap' && $file_surat_pengantar == 'Lengkap') {
        $status_berkas = 'Lengkap (Sesuai Syarat)';
    } elseif ($file_kk == 'Belum Lengkap' && $file_surat_pengantar == 'Belum Lengkap') {
        $status_berkas = 'Belum Lengkap';
    } else {
        // Jika salah satu sudah lengkap namun yang lain belum
        $status_berkas = 'Menunggu Verifikasi';
    }

    // Query UPDATE disesuaikan ke kolom asli database berdasarkan id
    $sql_update = "UPDATE pemberkasan_ktp SET 
                   file_kk = '$file_kk', 
                   file_surat_pengantar = '$file_surat_pengantar', 
                   status_berkas = '$status_berkas' 
                   WHERE id = $id";
    
    if ($conn->query($sql_update) === TRUE) {
        echo "<script>
                alert('Data pemberkasan berhasil diperbarui!');
                window.location.href='pemberkasan.php';
              </script>";
    } else {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Gagal memperbarui data: " . $conn->error . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Status Pemberkasan - PATEN Birayang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 700px;">
    
    <div class="mb-3">
        <a href="pemberkasan.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Batal</a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Update Status Berkas Pemohon</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Warga Pemohon</label>
                    <input type="text" class="form-control bg-white" value="<?php echo htmlspecialchars($data['nik'] . ' - ' . $data['nama']); ?>" disabled>
                </div>

                <hr>
                <h6 class="text-secondary mb-3 fw-bold">📋 Perbarui Ceklis Kelengkapan Berkas Fisik:</h6>

                <div class="mb-3">
                    <label class="form-label">1. Fotokopi Kartu Keluarga (KK)</label>
                    <select name="file_kk" class="form-select">
                        <option value="Belum Lengkap" <?php if(isset($data['file_kk']) && $data['file_kk'] == 'Belum Lengkap') echo 'selected'; ?>>❌ Belum Lengkap / Belum Kumpul</option>
                        <option value="Lengkap" <?php if(isset($data['file_kk']) && $data['file_kk'] == 'Lengkap') echo 'selected'; ?>>✅ Lengkap (Ada)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">2. Surat Pengantar RT/RW</label>
                    <select name="file_surat_pengantar" class="form-select">
                        <option value="Belum Lengkap" <?php if(isset($data['file_surat_pengantar']) && $data['file_surat_pengantar'] == 'Belum Lengkap') echo 'selected'; ?>>❌ Belum Lengkap / Belum Kumpul</option>
                        <option value="Lengkap" <?php if(isset($data['file_surat_pengantar']) && $data['file_surat_pengantar'] == 'Lengkap') echo 'selected'; ?>>✅ Lengkap (Ada)</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="update" class="btn btn-warning btn-lg fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Simpan Perubahan</button>
                </div>

            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>