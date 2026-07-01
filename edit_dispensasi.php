<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Tangkap ID yang mau diedit
if (!isset($_GET['id'])) {
    header("Location: dispensasi_nikah.php");
    exit();
}
$id = intval($_GET['id']);

// Proses Update Data jika tombol simpan ditekan
if (isset($_POST['update_dispensasi'])) {
    $nomor_surat = $_POST['nomor_surat'];
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
    $status = $_POST['status'];

    // Menangkap nilai checkbox (Jika dicentang = 'Ada', jika kosong = 'Tidak')
    $surat_pengantar = isset($_POST['surat_pengantar']) ? 'Ada' : 'Tidak';
    $blangko_n1_n7 = isset($_POST['blangko_n1_n7']) ? 'Ada' : 'Tidak';
    $surat_pindah_nikah = isset($_POST['surat_pindah_nikah']) ? 'Ada' : 'Tidak';
    $pas_foto = isset($_POST['pas_foto']) ? 'Ada' : 'Tidak';
    $fotokopi_akta_lahir_dan_ijazah_terakhir = isset($_POST['fotokopi_akta_lahir_dan_ijazah_terakhir']) ? 'Ada' : 'Tidak';

    // Query Update beserta kolom kelengkapan berkas
    $stmt = $conn->prepare("UPDATE dispensasi_nikah SET nomor_surat=?, tanggal_pengajuan=?, status=?, surat_pengantar=?, blangko_n1_n7=?, surat_pindah_nikah=?, pas_foto=?, fotokopi_akta_lahir_dan_ijazah_terakhir=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $nomor_surat, $tanggal_pengajuan, $status, $surat_pengantar, $blangko_n1_n7, $surat_pindah_nikah, $pas_foto, $fotokopi_akta_lahir_dan_ijazah_terakhir, $id);
    
    if ($stmt->execute()) {
        header("Location: dispensasi_nikah.php?pesan=diedit");
        exit();
    }
    $stmt->close();
}

// Ambil data lama beserta nilai ceklis untuk ditampilkan di form
$query = $conn->query("SELECT d.*, w.nama FROM dispensasi_nikah d LEFT JOIN warga w ON d.nik = w.nik WHERE d.id = $id");
$data = $query->fetch_assoc();

// Mencegah error jika ID pengajuan tidak ada di database
if (!$data) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h3>❌ Error: Data pengajuan tidak ditemukan!</h3>
            <a href='dispensasi_nikah.php'>Kembali ke Daftar Dispensasi</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Dispensasi - PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5" style="max-width: 650px;">
        <div class="card border-0 shadow-sm p-4">
            <h4 class="fw-bold text-warning mb-4"><i class="bi bi-pencil-square me-2"></i> Edit Pengajuan Dispensasi</h4>
            
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label text-muted">Nama Warga</label>
                    <input type="text" class="form-control bg-light" 
                           value="<?php echo htmlspecialchars($data['nama'] ?? 'Nama Tidak Terdata'); ?>" 
                           readonly>
                </div>

                <div class="mb-4 p-3 border rounded bg-white">
                    <label class="form-label fw-bold text-secondary mb-3"><i class="bi bi-file-earmark-check-fill text-success me-1"></i> Kelengkapan Syarat Berkas</label>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="surat_pengantar" id="surat_pengantar" value="Ada" <?php if(($data['surat_pengantar'] ?? '') == 'Ada') echo 'checked'; ?>>
                        <label class="form-check-label" for="surat_pengantar">Surat Pengantar RT/RW</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="blangko_n1_n7" id="blangko_n1_n7" value="Ada" <?php if(($data['blangko_n1_n7'] ?? '') == 'Ada') echo 'checked'; ?>>
                        <label class="form-check-label" for="blangko_n1_n7">Blangko N1 sampai N7 dari Kelurahan/Desa</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="surat_pindah_nikah" id="surat_pindah_nikah" value="Ada" <?php if(($data['surat_pindah_nikah'] ?? '') == 'Ada') echo 'checked'; ?>>
                        <label class="form-check-label" for="surat_pindah_nikah">Surat Pindah Nikah (Jika berbeda domisili)</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="pas_foto" id="pas_foto" value="Ada" <?php if(($data['pas_foto'] ?? '') == 'Ada') echo 'checked'; ?>>
                        <label class="form-check-label" for="pas_foto">Pas Foto 2x3 & 3x4 (Masing-masing 5 Lembar)</label>
                    </div>

                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="fotokopi_akta_lahir_dan_ijazah_terakhir" id="fotokopi_akta_lahir_dan_ijazah_terakhir" value="Ada" <?php if(($data['fotokopi_akta_lahir_dan_ijazah_terakhir'] ?? '') == 'Ada') echo 'checked'; ?>>
                        <label class="form-check-label" for="fotokopi_akta_lahir_dan_ijazah_terakhir">Fotokopi Akta Kelahiran & Ijazah Terakhir</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Surat Rekomendasi</label>
                    <input type="text" name="nomor_surat" class="form-control" 
                           value="<?php echo htmlspecialchars($data['nomor_surat'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" class="form-control" 
                           value="<?php echo htmlspecialchars($data['tanggal_pengajuan'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status Berkas</label>
                    <select name="status" class="form-select" required>
                        <option value="Menunggu Verifikasi" <?php if(($data['status'] ?? '') == 'Menunggu Verifikasi') echo 'selected'; ?>>Menunggu Verifikasi</option>
                        <option value="Lengkap (Sesuai Syarat)" <?php if(($data['status'] ?? '') == 'Lengkap (Sesuai Syarat)') echo 'selected'; ?>>Lengkap (Sesuai Syarat)</option>
                        <option value="Ditolak / Tidak Memenuhi" <?php if(($data['status'] ?? '') == 'Ditolak / Tidak Memenuhi') echo 'selected'; ?>>Ditolak / Tidak Memenuhi</option>
                    </select>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="dispensasi_nikah.php" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" name="update_dispensasi" class="btn btn-warning fw-bold px-4"><i class="bi bi-save-fill me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>