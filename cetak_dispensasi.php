<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='text-align:center; margin-top:50px;'><h3>❌ Error: ID Pengajuan tidak ditemukan!</h3><a href='dispensasi_nikah.php'>Kembali</a></div>");
}

$id_dispensasi = intval($_GET['id']);

$query = "SELECT d.id, d.nik, d.nomor_surat, d.tanggal_pengajuan, d.status, 
                 d.surat_pengantar, d.blangko_n1_n7, d.surat_pindah_nikah, d.pas_foto, 
                 d.fotokopi_akta_lahir_dan_ijazah_terakhir, w.nama 
          FROM dispensasi_nikah d 
          LEFT JOIN warga w ON d.nik = w.nik 
          WHERE d.id = ?";
          
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_dispensasi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div style='text-align:center; margin-top:50px;'><h3>❌ Error: Data tidak ditemukan!</h3><a href='dispensasi_nikah.php'>Kembali</a></div>");
}

$data = $result->fetch_assoc();
$stmt->close();

function tgl_indo($tanggal) {
    if(!$tanggal) return '-';
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Dispensasi Nikah - <?php echo htmlspecialchars($data['nama'] ?? 'Warga'); ?></title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 10px 40px; /* Diperketat sedikit margin atasnya agar aman 1 halaman */
            line-height: 1.35;
        }
        .kop-surat {
            border-bottom: 3px solid #000; /* Garis tebal tunggal sesuai pratinjau */
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .kop-tabel {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .kop-logo {
            width: 12%;
            text-align: left;
            vertical-align: middle;
        }
        .kop-logo img {
            width: 80px; /* Ukuran logo disesuaikan proporsinya */
            height: auto;
            display: block;
        }
        .kop-teks {
            width: 88%;
            text-align: center;
            vertical-align: middle;
            padding-right: 80px; /* Menyeimbangkan posisi teks agar tepat berada di tengah halaman */
        }
        .kop-teks h1 {
            font-size: 14px;
            text-transform: uppercase;
            margin: 0 0 2px 0;
            font-weight: normal; /* Sesuai pratinjau yang tidak terlalu tebal */
        }
        .kop-teks h2 {
            font-size: 18px;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            font-weight: bold;
        }
        .kop-teks p {
            font-size: 11px;
            font-style: italic;
            margin: 0;
        }
        .judul-surat {
            text-align: center;
            margin-bottom: 15px;
        }
        .judul-surat h3 {
            font-size: 15px;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 3px 0;
        }
        .judul-surat p {
            margin: 0;
            font-size: 13px;
        }
        .paragraf {
            text-align: justify;
            text-indent: 40px;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .tabel-data {
            width: 95%;
            margin: 10px auto;
            border-collapse: collapse;
            font-size: 13px;
        }
        .tabel-data td {
            padding: 4px;
            vertical-align: top;
        }
        .tabel-syarat {
            width: 100%;
            margin: 10px auto;
            border: 1px solid #000;
            border-collapse: collapse;
            font-size: 12px;
        }
        .tabel-syarat th, .tabel-syarat td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        .tabel-syarat th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .ttd-container {
            width: 100%;
            margin-top: 15px;
            font-size: 13px;
        }
        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .ttd-space {
            height: 60px; /* Memastikan ruang tanda tangan pas dan mengunci dokumen di 1 halaman */
        }
        .no-print {
            background: #f8f9fa;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            padding: 6px 12px;
            text-decoration: none;
            color: #fff;
            border-radius: 4px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .btn-kembali { background-color: #6c757d; }
        .btn-cetak { background-color: #198754; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <span style="font-family: Arial, sans-serif; font-size: 13px; color: #333;">
            ℹ️ Lembar cetak dokumen PATEN Kecamatan Birayang.
        </span>
        <div>
            <a href="dispensasi_nikah.php" class="btn btn-kembali">⬅️ Kembali</a>
            <a href="#" onclick="window.print(); return false;" class="btn btn-cetak">🖨️ Cetak</a>
        </div>
    </div>

    <div class="kop-surat">
        <table class="kop-tabel">
            <tr>
                <td class="kop-logo">
                    <img src="Logo_Kecamatan.png" alt="Logo Kecamatan">
                </td>
                <td class="kop-teks">
                    <h1>Pemerintah Kabupaten Hulu Sungai Tengah</h1>
                    <h2>Kecamatan Birayang</h2>
                    <p>Jl. Kesatria RT. 002 RW. 001 Kode Pos 71351</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="judul-surat">
        <h3>Surat Keterangan Dispensasi Waktu Nikah</h3>
        <p>Nomor: <?php echo htmlspecialchars($data['nomor_surat'] ?? ''); ?></p>
    </div>

    <p class="paragraf">
        Yang bertanda tangan di bawah ini Camat Birayang Kabupaten Hulu Sungai Tengah, menerangkan bahwa permohonan dispensasi waktu pernikahan dari warga dengan identitas di bawah ini:
    </p>

    <table class="tabel-data">
        <tr>
            <td style="width: 35%;">Nama Lengkap</td>
            <td style="width: 3%;">:</td>
            <td style="font-weight: bold;"><?php echo htmlspecialchars($data['nama'] ?? 'Data Warga Tidak Ditemukan'); ?></td>
        </tr>
        <tr>
            <td>Nomor Induk Kependudukan (NIK)</td>
            <td>:</td>
            <td><?php echo htmlspecialchars($data['nik'] ?? ''); ?></td>
        </tr>
        <tr>
            <td>Tanggal Pengajuan Surat</td>
            <td>:</td>
            <td><?php echo tgl_indo($data['tanggal_pengajuan'] ?? ''); ?></td>
        </tr>
        <tr>
            <td>Status Kelayakan Berkas</td>
            <td>:</td>
            <td><strong><?php echo htmlspecialchars($data['status'] ?? ''); ?></strong></td>
        </tr>
    </table>

    <p class="paragraf">
        Berdasarkan hasil verifikasi berkas persyaratan administratif Pelayanan Administrasi Terpadu Kecamatan (PATEN) Birayang, berikut rincian kelengkapan dokumen pendukung:
    </p>

    <table class="tabel-syarat">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th>Komponen Berkas Persyaratan Dispensasi</th>
                <th style="width: 25%;">Status Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Surat Pengantar RT/RW</td>
                <td style="text-align: center; font-weight: bold;"><?php echo (($data['surat_pengantar'] ?? '') == 'Ada') ? '✔️ ADA' : '❌ TIDAK ADA'; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Blangko N1 sampai N7 Kelurahan/Desa</td>
                <td style="text-align: center; font-weight: bold;"><?php echo (($data['blangko_n1_n7'] ?? '') == 'Ada') ? '✔️ ADA' : '❌ TIDAK ADA'; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Surat Pindah Nikah</td>
                <td style="text-align: center; font-weight: bold;"><?php echo (($data['surat_pindah_nikah'] ?? '') == 'Ada') ? '✔️ ADA' : '💡 TIDAK PERLU'; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>Pas Foto Ukuran 2x3 & 3x4 (Masing-masing 5 Lembar)</td>
                <td style="text-align: center; font-weight: bold;"><?php echo (($data['pas_foto'] ?? '') == 'Ada') ? '✔️ ADA' : '❌ TIDAK ADA'; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;">5</td>
                <td>Fotokopi Akta Kelahiran & Ijazah Terakhir</td>
                <td style="text-align: center; font-weight: bold;"><?php echo (($data['fotokopi_akta_lahir_dan_ijazah_terakhir'] ?? '') == 'Ada') ? '✔️ ADA' : '❌ TIDAK ADA'; ?></td>
            </tr>
        </tbody>
    </table>

    <p class="paragraf">
        Demikian surat keterangan dispensasi waktu nikah ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Birayang, <?php echo tgl_indo($data['tanggal_pengajuan'] ?? date('Y-m-d')); ?></p>
            <div class="ttd-space"></div>
            <p style="font-size: 14px; font-weight: bold; margin-bottom: 0;">
                <u>( Camat Birayang )</u>
            </p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        };
    </script>
</body>
</html>