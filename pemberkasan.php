<?php
// 1. Tambahkan Proteksi Session untuk Keamanan Area Admin
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 2. Koneksi ke database
require_once 'koneksi.php';

// Menangani pencarian jika admin mengetik nama/NIK di kolom cari
$search = "";
if (isset($_GET['cari'])) {
    $search = $conn->real_escape_string($_GET['cari']);
}

// Query mengambil data dari tabel pemberkasan dan mencocokkannya dengan nama dari tabel warga
$sql = "SELECT p.*, w.nama FROM pemberkasan_ktp p 
        JOIN warga w ON p.nik = w.nik";

if (!empty($search)) {
    $sql .= " WHERE p.nik LIKE '%$search%' OR w.nama LIKE '%$search%'";
}

// Menggunakan nama kolom waktu_verifikasi sesuai struktur asli database pemberkasan_ktp
$sql .= " ORDER BY p.waktu_verifikasi DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemberkasan KTP - PATEN Birayang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        @media print {
            .no-print, .btn, form, .modal, th:last-child, td:last-child {
                display: none !important;
            }
            body { background-color: white !important; padding: 0; }
            .card { border: none !important; box-shadow: none !important; }
            .card-header { background-color: white !important; color: black !important; border-bottom: 2px solid black !important; padding-left: 0 !important; }
            .table { width: 100% !important; border-collapse: collapse !important; }
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="mb-3 no-print">
        <a href="index.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text-fill me-2"></i> Modul Pemberkasan KTP Kecamatan Birayang</h5>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-warning btn-sm me-2 fw-bold text-dark">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                </button>
                <a href="tambah_pemberkasan.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> Buat Pengajuan Baru</a>
            </div>
        </div>
        <div class="card-body p-4">
            
            <form method="GET" action="" class="mb-4 no-print">
                <div class="row g-2">
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="text" name="cari" class="form-control" placeholder="Cari berdasarkan NIK atau Nama warga..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">Cari</button>
                            <?php if (isset($_GET['cari'])): ?>
                                <a href="pemberkasan.php" class="btn btn-outline-secondary">Reset</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>NIK</th>
                            <th>Nama Warga</th>
                            <th>Berkas KK</th>
                            <th>Berkas Pengantar</th>
                            <th>Status Kelayakan</th>
                            <th width="18%" class="no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            $no = 1;
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='text-center'>".$no++."</td>";
                                echo "<td><strong>".htmlspecialchars($row['nik'])."</strong></td>";
                                echo "<td>".htmlspecialchars($row['nama'])."</td>";
                                
                                // Variabel penentu keberadaan berkas fisik
                                $kk_ada = !empty($row['file_kk']);
                                $pengantar_ada = !empty($row['file_surat_pengantar']);

                                // 1. Tampilan Kolom Berkas KK
                                if ($kk_ada) {
                                    echo "<td class='text-center'><span class='badge bg-success'><i class='bi bi-check-circle-fill me-1'></i> Lengkap</span></td>";
                                } else {
                                    echo "<td class='text-center'><span class='badge bg-danger'><i class='bi bi-x-circle-fill me-1'></i> Belum Lengkap</span></td>";
                                }
                                
                                // 2. Tampilan Kolom Berkas Pengantar
                                if ($pengantar_ada) {
                                    echo "<td class='text-center'><span class='badge bg-success'><i class='bi bi-check-circle-fill me-1'></i> Lengkap</span></td>";
                                } else {
                                    echo "<td class='text-center'><span class='badge bg-danger'><i class='bi bi-x-circle-fill me-1'></i> Belum Lengkap</span></td>";
                                }
                                
                                // 3. Logika Fleksibel untuk Status Kelayakan (Kuning jika hanya salah satu yang lengkap)
                                if (($kk_ada && !$pengantar_ada) || (!$kk_ada && $pengantar_ada)) {
                                    $status_class = 'bg-warning text-dark';
                                } else {
                                    // Jika dua-duanya ada atau dua-duanya kosong, ikuti nilai aslinya
                                    $status_class = 'bg-warning text-dark';
                                    if ($row['status_berkas'] == 'Lengkap (Sesuai Syarat)') $status_class = 'bg-success';
                                    if ($row['status_berkas'] == 'Belum Lengkap') $status_class = 'bg-danger';
                                }
                                
                                echo "<td class='text-center'><span class='badge $status_class' style='font-size: 13px;'>".htmlspecialchars($row['status_berkas'])."</span></td>";
                                
                                // Menggunakan $row['id'] yang mengarah ke primary key tabel pemberkasan_ktp
                                echo "<td class='text-center no-print'>
                                        <div class='btn-group' role='group'>
                                            <a href='cetak_tanda_terima.php?id=".$row['id']."' target='_blank' class='btn btn-info btn-sm text-white' title='Cetak Tanda Terima'><i class='bi bi-printer-fill'></i></a>
                                            <a href='edit_pemberkasan.php?id=".$row['id']."' class='btn btn-warning btn-sm' title='Ubah Status'><i class='bi bi-pencil-square'></i></a>
                                            <button type='button' class='btn btn-danger btn-sm' 
                                                    data-bs-toggle='modal' 
                                                    data-bs-target='#konfirmasiHapusModal' 
                                                    data-id='".$row['id']."' 
                                                    data-nama='".htmlspecialchars($row['nama'], ENT_QUOTES)."' 
                                                    title='Hapus'>
                                                <i class='bi bi-trash-fill'></i>
                                            </button>
                                        </div>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted py-4'>Belum ada riwayat berkas pengajuan KTP yang diproses.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="konfirmasiHapusModal" tabindex="-1" aria-labelledby="konfirmasiHapusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="konfirmasiHapusModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-1">Apakah Anda yakin ingin menghapus seluruh riwayat berkas pengajuan KTP milik:</p>
                <h5 class="text-dark fw-bold" id="namaWargaHapus"></h5>
                <small class="text-danger">*Tindakan ini tidak dapat dibatalkan dan data akan hilang permanen dari database.</small>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="tombolEksekusiHapus" class="btn btn-danger fw-bold">Ya, Hapus Data</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalHapus = document.getElementById('konfirmasiHapusModal');
    if (modalHapus) {
        modalHapus.addEventListener('show.bs.modal', function (event) {
            const tombol = event.relatedTarget;
            const idBerkas = tombol.getAttribute('data-id');
            const namaWarga = tombol.getAttribute('data-nama');
            const teksNama = modalHapus.querySelector('#namaWargaHapus');
            teksNama.textContent = namaWarga;
            const linkHapus = modalHapus.querySelector('#tombolEksekusiHapus');
            linkHapus.href = 'hapus_pemberkasan.php?id=' + idBerkas;
        });
    }
</script>
</body>
</html>
<?php $conn->close(); ?>