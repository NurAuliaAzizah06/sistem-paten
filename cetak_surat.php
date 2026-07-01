<?php
// 1. Proteksi Session Keamanan Area Admin
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 2. Koneksi ke Database
require_once 'koneksi.php';

// PERUBAHAN UTAMA: Menggunakan LEFT JOIN untuk menarik data status_berkas dari tabel pemberkasan_ktp
$sql = "SELECT w.*, p.status_berkas 
        FROM warga w 
        LEFT JOIN pemberkasan_ktp p ON w.nik = p.nik 
        ORDER BY w.waktu_input DESC";
$result = $conn->query($sql);

// Fungsi pembantu konversi tanggal ke format resmi Bahasa Indonesia
function tgl_indo($waktu) {
    if (empty($waktu)) return "-";
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $split = explode('-', date('Y-m-d', strtotime($waktu)));
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Data Warga - PATEN Birayang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
            padding: 30px;
        }
        /* Menghilangkan komponen tombol cetak saat kertas diprint fisik */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print();"> 

    <div class="no-print mb-4 text-end">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Laporan</button>
        <a href="lihat_warga.php" class="btn btn-secondary">Kembali</a>
    </div>

    <div style="display: flex; align-items: center; justify-content: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 30px;">
        <div style="flex: 1; text-align: left; max-width: 90px;">
            <img src="Logo_Kecamatan.png" alt="Logo Daerah" style="width: 75px; height: auto;">
        </div>
        
        <div style="flex: 4; text-align: center; font-family: 'Times New Roman', Times, serif;">
            <h4 style="margin: 0; text-transform: uppercase; font-size: 16px; letter-spacing: 0.5px;">PEMERINTAH KABUPATEN HULU SUNGAI TENGAH</h4>
            <h3 style="margin: 4px 0; text-transform: uppercase; font-size: 22px; font-weight: bold;">KECAMATAN BIRAYANG</h3>
            <p style="margin: 0; font-size: 12px; font-style: italic; color: #333;">
                Jl. Kesatria RT. 002 RW.001 Kode Pos 71351
            </p>
        </div>
        <div style="flex: 1; max-width: 90px;"></div>
    </div>
    
    <div class="text-center mb-4">
        <h4 style="font-family: 'Times New Roman', serif; font-weight: bold; text-transform: uppercase;">LAPORAN DATA KEPENDUDUKAN<br>KECAMATAN BIRAYANG</h4>
    </div>

    <table class="table table-bordered align-middle" style="font-family: 'Times New Roman', serif; font-size: 15px;">
        <thead class="table-light text-center fw-bold">
            <tr>
                <th width="5%">No</th>
                <th width="20%">NIK</th>
                <th width="30%">Nama Warga</th>
                <th width="25%">Waktu Terdaftar</th>
                <th width="20%">Status Berkas KTP</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result && $result->num_rows > 0) {
                $no = 1;
                while($row = $result->fetch_assoc()) {
                    // Penanganan jika warga belum mengajukan pemberkasan berkas fisik sama sekali
                    $status_berkas = !empty($row['status_berkas']) ? $row['status_berkas'] : 'Belum Ada Berkas';
                    
                    echo "<tr>";
                    echo "<td class='text-center'>".$no++."</td>";
                    echo "<td>".htmlspecialchars($row['nik'])."</td>";
                    echo "<td>".htmlspecialchars($row['nama'])."</td>";
                    // Konversi waktu input otomatis ke format Indonesia yang rapi
                    echo "<td class='text-center'>".tgl_indo($row['waktu_input'])."</td>";
                    echo "<td class='text-center'>".htmlspecialchars($status_berkas)."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada data warga terdaftar.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="row mt-5" style="font-family: 'Times New Roman', serif; font-size: 16px;">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p>Birayang, <?php echo tgl_indo(date('Y-m-d')); ?></p>
            <br><br><br>
            <p class="fw-bold" style="text-decoration: underline;">( Admin PATEN )</p>
        </div>
    </div>

</body>
</html>
<?php $conn->close(); ?>