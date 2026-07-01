<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$pesan = "";

// Tangkap Notifikasi Pesan Sukses atau Gagal
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'sukses') $pesan = "<div class='alert alert-success shadow-sm'>🎉 Data Dispensasi Nikah berhasil ditambahkan!</div>";
    elseif ($_GET['pesan'] == 'dihapus') $pesan = "<div class='alert alert-success shadow-sm'>🗑️ Data berhasil dihapus!</div>";
    elseif ($_GET['pesan'] == 'diedit') $pesan = "<div class='alert alert-success shadow-sm'>📝 Data berhasil diperbarui!</div>";
    elseif ($_GET['pesan'] == 'gagal_berkas') $pesan = "<div class='alert alert-danger shadow-sm fw-bold'>❌ Gagal Simpan! Warga yang bersangkutan wajib menyelesaikan & melengkapi dokumen di Modul Pemberkasan KTP terlebih dahulu.</div>";
}

// Proses Hapus Data
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $conn->query("DELETE FROM dispensasi_nikah WHERE id = $id_hapus");
    header("Location: dispensasi_nikah.php?pesan=dihapus");
    exit();
}

// Proses Simpan Data Pengajuan Baru
if (isset($_POST['simpan_dispensasi'])) {
    $nik = $_POST['nik'];
    $nomor_surat = $_POST['nomor_surat'];
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
    $status = $_POST['status'];
    
    // Nilai checklist disesuaikan
    $surat_pengantar = isset($_POST['surat_pengantar']) ? 'Ada' : 'Tidak';
    $blangko_n1_n7 = isset($_POST['blangko_n1_n7']) ? 'Ada' : 'Tidak';
    $surat_pindah_nikah = isset($_POST['surat_pindah_nikah']) ? 'Ada' : 'Tidak';
    $pas_foto = isset($_POST['pas_foto']) ? 'Ada' : 'Tidak';
    $fotokopi_akta_lahir_dan_ijazah_terakhir = isset($_POST['fotokopi_akta_lahir_dan_ijazah_terakhir']) ? 'Ada' : 'Tidak';

    // ─── AMAN & TERINTEGRASI: Cek Ulang Status Pemberkasan KTP Warga di Backend ───
    $check_berkas = $conn->prepare("SELECT status_berkas FROM pemberkasan_ktp WHERE nik = ?");
    $check_berkas->bind_param("s", $nik);
    $check_berkas->execute();
    $res_berkas = $check_berkas->get_result()->fetch_assoc();
    $check_berkas->close();

    // Jika data tidak ditemukan atau statusnya bukan 'Lengkap (Sesuai Syarat)'
    if (!$res_berkas || $res_berkas['status_berkas'] !== 'Lengkap (Sesuai Syarat)') {
        header("Location: dispensasi_nikah.php?pesan=gagal_berkas");
        exit(); // Blokir aksi insert
    }
    // ───────────────────────────────────────────────────────────────────────────────

    $stmt = $conn->prepare("INSERT INTO dispensasi_nikah (nik, nomor_surat, tanggal_pengajuan, status, surat_pengantar, blangko_n1_n7, surat_pindah_nikah, pas_foto, fotokopi_akta_lahir_dan_ijazah_terakhir) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $nik, $nomor_surat, $tanggal_pengajuan, $status, $surat_pengantar, $blangko_n1_n7, $surat_pindah_nikah, $pas_foto, $fotokopi_akta_lahir_dan_ijazah_terakhir);
    
    if ($stmt->execute()) {
        header("Location: dispensasi_nikah.php?pesan=sukses");
        exit();
    } else {
        $pesan = "<div class='alert alert-danger shadow-sm'>❌ Gagal menyimpan data: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// ─── MODIFIKASI QUERY: LEFT JOIN dengan tabel pemberkasan_ktp untuk mengambil status_berkas KTP ───
$pilihan_warga = $conn->query("SELECT w.nik, w.nama, p.status_berkas 
                              FROM warga w 
                              LEFT JOIN pemberkasan_ktp p ON w.nik = p.nik 
                              ORDER BY w.nama ASC");

// Ambil Data Tabel Riwayat
$daftar_dispensasi = $conn->query("SELECT d.*, w.nama FROM dispensasi_nikah d LEFT JOIN warga w ON d.nik = w.nik ORDER BY d.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dispensasi Nikah - PATEN Birayang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left-circle-fill me-2"></i> Kembali ke Dashboard</a>
            <span class="navbar-text text-white fw-bold">Pelayanan Dispensasi Nikah (Dispensasi Waktu)</span>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        <?php echo $pesan; ?>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-ui-checks-grid me-2"></i> Input Pengajuan Baru</h5>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Warga</label>
                            <select name="nik" class="form-select" required>
                                <option value="">-- Pilih Warga --</option>
                                <?php while($row = $pilihan_warga->fetch_assoc()): 
                                    // Validasi kelayakan berkas KTP warga
                                    $is_lengkap = ($row['status_berkas'] == 'Lengkap (Sesuai Syarat)');
                                    
                                    if ($is_lengkap): ?>
                                        <option value="<?php echo $row['nik']; ?>">
                                            <?php echo htmlspecialchars($row['nama']); ?> (<?php echo htmlspecialchars($row['nik']); ?>)
                                        </option>
                                    <?php else: 
                                        $status_sekarang = $row['status_berkas'] ?? 'Belum Ada Berkas'; ?>
                                        <option value="" disabled class="text-muted text-decoration-line-through">
                                            <?php echo htmlspecialchars($row['nama']); ?> (🔒 Berkas KTP: <?php echo htmlspecialchars($status_sekarang); ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            </select>
                            <small class="text-muted d-block mt-1">*Warga dengan berkas KTP tidak lengkap otomatis terkunci.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Surat</label>
                            <input type="text" name="nomor_surat" class="form-control" placeholder="Contoh: 400/12/Kec-Byg" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Pengajuan</label>
                            <input type="date" name="tanggal_pengajuan" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3 p-3 bg-light border rounded">
                            <label class="form-label fw-bold text-dark border-bottom pb-2 mb-2 d-block">Kelengkapan Syarat Berkas</label>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="surat_pengantar" value="Ada" id="cek1">
                                <label class="form-check-label" for="cek1">Surat Pengantar RT/RW</label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="blangko_n1_n7" value="Ada" id="cek2">
                                <label class="form-check-label" for="cek2">Blangko N1-N7 Kelurahan/Desa</label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="surat_pindah_nikah" value="Ada" id="cek3">
                                <label class="form-check-label" for="cek3">Surat Pindah Nikah (Bila Beda Domisili)</label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="pas_foto" value="Ada" id="cek4">
                                <label class="form-check-label" for="cek4">Pas Foto 2x3 & 3x4 (5 Lembar)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fotokopi_akta_lahir_dan_ijazah_terakhir" value="Ada" id="cek5">
                                <label class="form-check-label" for="cek5">Fotokopi Akta Kelahiran & Ijazah</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Berkas</label>
                            <select name="status" class="form-select" required>
                                <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                                <option value="Lengkap (Sesuai Syarat)">Lengkap (Sesuai Syarat)</option>
                                <option value="Ditolak / Tidak Memenuhi">Ditolak / Tidak Memenuhi</option>
                            </select>
                        </div>
                        <button type="submit" name="simpan_dispensasi" class="btn btn-danger w-100 fw-bold py-2"><i class="bi bi-save me-2"></i> Simpan Data</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-table me-2"></i> Daftar Riwayat Dispensasi Nikah</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" style="font-size: 0.9em;">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Data Warga & No. Surat</th>
                                    <th>Kelengkapan Berkas</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($daftar_dispensasi->num_rows > 0): ?>
                                    <?php $no = 1; while($data = $daftar_dispensasi->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?php echo $no++; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($data['nama'] ?? 'Warga Dihapus'); ?></strong><br>
                                                <small class="text-muted"><i class="bi bi-hash"></i> <?php echo htmlspecialchars($data['nomor_surat']); ?></small><br>
                                                <small class="text-muted"><i class="bi bi-calendar-event"></i> <?php echo date('d M Y', strtotime($data['tanggal_pengajuan'])); ?></small>
                                            </td>
                                            <td>
                                                <ul class="mb-0 ps-3 text-muted" style="font-size: 0.9em; list-style-type: square;">
                                                    <li>Pengantar RT/RW: <?php echo ($data['surat_pengantar'] ?? 'Tidak') == 'Ada' ? '<span class="text-success fw-bold">Ada</span>' : '<span class="text-danger">Tidak</span>'; ?></li>
                                                    <li>Blangko N1-N7: <?php echo ($data['blangko_n1_n7'] ?? 'Tidak') == 'Ada' ? '<span class="text-success fw-bold">Ada</span>' : '<span class="text-danger">Tidak</span>'; ?></li>
                                                    <li>Surat Pindah: <?php echo ($data['surat_pindah_nikah'] ?? 'Tidak') == 'Ada' ? '<span class="text-success fw-bold">Ada</span>' : '<span class="text-danger">Tidak</span>'; ?></li>
                                                    <li>Pas Foto: <?php echo ($data['pas_foto'] ?? 'Tidak') == 'Ada' ? '<span class="text-success fw-bold">Ada</span>' : '<span class="text-danger">Tidak</span>'; ?></li>
                                                    <li>Akta & Ijazah: <?php echo ($data['fotokopi_akta_lahir_dan_ijazah_terakhir'] ?? 'Tidak') == 'Ada' ? '<span class="text-success fw-bold">Ada</span>' : '<span class="text-danger">Tidak</span>'; ?></li>
                                                </ul>
                                            </td>
                                            <td class="text-center">
                                                <?php if($data['status'] == 'Lengkap (Sesuai Syarat)'): ?>
                                                    <span class="badge bg-success">Lengkap</span>
                                                <?php elseif($data['status'] == 'Menunggu Verifikasi'): ?>
                                                    <span class="badge bg-warning text-dark">Proses</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Ditolak</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group-vertical btn-group-sm" role="group">
                                                    <a href="cetak_dispensasi.php?id=<?php echo $data['id']; ?>" target="_blank" class="btn btn-dark mb-1 rounded"><i class="bi bi-printer-fill me-1"></i> Cetak</a>
                                                    <a href="edit_dispensasi.php?id=<?php echo $data['id']; ?>" class="btn btn-warning mb-1 rounded"><i class="bi bi-pencil-fill me-1"></i> Edit</a>
                                                    <a href="dispensasi_nikah.php?hapus=<?php echo $data['id']; ?>" onclick="return confirm('Hapus data milik <?php echo addslashes($data['nama']); ?>?');" class="btn btn-danger rounded"><i class="bi bi-trash-fill me-1"></i> Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada data pengajuan dispensasi.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>