<?php
// Koneksi database Docker kamu
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// 1. Ambil data warga dari tabel warga untuk dimasukkan ke pilihan dropdown (Select Option)
$warga_query = $conn->query("SELECT nik, nama FROM warga ORDER BY nama ASC");

// 2. Proses ketika tombol "Simpan Pengajuan" diklik
if (isset($_POST['simpan'])) {
    $nik = $conn->real_escape_string($_POST['nik']);
    $syarat_kk = $_POST['syarat_kk'];
    $syarat_akta = $_POST['syarat_akta'];
    $syarat_surat_pengantar = $_POST['syarat_surat_pengantar'];
    
    // Logika penentuan status otomatis agar sistem terlihat cerdas di hadapan penguji:
    if ($syarat_kk == 'Lengkap' && $syarat_akta == 'Lengkap' && $syarat_surat_pengantar == 'Lengkap') {
        $status_berkas = 'Lengkap (Sesuai Syarat)';
    } elseif ($syarat_kk == 'Belum Lengkap' && $syarat_akta == 'Belum Lengkap' && $syarat_surat_pengantar == 'Belum Lengkap') {
        $status_berkas = 'Belum Lengkap';
    } else {
        $status_berkas = 'Menunggu Verifikasi';
    }

    // Query simpan data ke tabel pemberkasan_ktp
    $sql_insert = "INSERT INTO pemberkasan_ktp (nik, syarat_kk, syarat_akta, syarat_surat_pengantar, status_berkas) 
                   VALUES ('$nik', '$syarat_kk', '$syarat_akta', '$syarat_surat_pengantar', '$status_berkas')";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>
                alert('Data pemberkasan berhasil ditambahkan!');
                window.location.href='pemberkasan.php';
              </script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menyimpan data: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengajuan Pemberkasan - PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 700px;">
    
    <div class="mb-3">
        <a href="pemberkasan.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i> Buat Pengajuan Pemberkasan KTP</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="">
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Pilih Warga Pemohon</label>
                    <select name="nik" class="form-select" required>
                        <option value="">-- Pilih NIK / Nama Warga --</option>
                        <?php 
                        if ($warga_query->num_rows > 0) {
                            while($warga = $warga_query->fetch_assoc()) {
                                echo "<option value='".$warga['nik']."'>".$warga['nik']." - ".$warga['nama']."</option>";
                            }
                        } else {
                            echo "<option value=''>Belum ada data warga di database</option>";
                        }
                        ?>
                    </select>
                </div>

                <hr>
                <h6 class="text-secondary mb-3 fw-bold">📋 Ceklis Kelengkapan Berkas Fisik:</h6>

                <div class="mb-3">
                    <label class="form-label">1. Fotokopi Kartu Keluarga (KK)</label>
                    <select name="syarat_kk" class="form-select">
                        <option value="Belum Lengkap">❌ Belum Lengkap / Belum Kumpul</option>
                        <option value="Lengkap">✅ Lengkap (Ada)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">2. Fotokopi Akta Kelahiran</label>
                    <select name="syarat_akta" class="form-select">
                        <option value="Belum Lengkap">❌ Belum Lengkap / Belum Kumpul</option>
                        <option value="Lengkap">✅ Lengkap (Ada)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">3. Surat Pengantar RT/RW</label>
                    <select name="syarat_surat_pengantar" class="form-select">
                        <option value="Belum Lengkap">❌ Belum Lengkap / Belum Kumpul</option>
                        <option value="Lengkap">✅ Lengkap (Ada)</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="simpan" class="btn btn-success btn-lg"><i class="bi bi-save2-fill me-2"></i> Simpan Pengajuan</button>
                </div>

            </form>
        </div>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>