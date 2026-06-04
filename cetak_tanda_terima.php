<?php
// 1. Proteksi Session Keamanan Admin
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

// 3. Ambil ID Pemberkasan dari URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Query disesuaikan dengan struktur p.id
    $query = $conn->query("SELECT p.*, w.nama FROM pemberkasan_ktp p 
                           JOIN warga w ON p.nik = w.nik 
                           WHERE p.id = $id");
    $data = $query->fetch_assoc();
    
    if (!$data) { 
        die("<div style='padding:20px; text-align:center; font-family:sans-serif;'><h3>Data tanda terima tidak ditemukan!</h3></div>"); 
    }
} else {
    header("Location: pemberkasan.php");
    exit;
}

// 4. Fungsi khusus konversi tanggal ke format resmi Bahasa Indonesia
function tgl_indo($waktu) {
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
    <title>Tanda Terima Berkas - <?php echo htmlspecialchars($data['nama']); ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 30px; line-height: 1.6; background-color: white; color: black; }
        
        /* LAYOUT KOP Surat dengan Logo di Kiri */
        .kop-container { display: flex; align-items: center; border-bottom: 3px double black; padding-bottom: 15px; margin-bottom: 30px; }
        .kop-logo { width: 80px; height: auto; padding-right: 20px; }
        .kop-teks { flex-grow: 1; text-align: center; padding-right: 60px; }
        
        .kop-teks h2 { margin: 0; text-transform: uppercase; font-size: 18px; letter-spacing: 0.5px; line-height: 1.2; }
        .kop-teks h3 { margin: 5px 0; font-size: 20px; font-weight: bold; line-height: 1.2; }
        .kop-teks p { margin: 0; font-size: 12px; font-style: italic; }
        
        .judul-surat { text-align: center; font-weight: bold; text-decoration: underline; font-size: 18px; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .tabel-data { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .tabel-data td { padding: 6px 4px; vertical-align: top; font-size: 16px; }
        
        .tabel-ceklis { width: 100%; border: 1px solid black; border-collapse: collapse; margin-bottom: 30px; }
        .tabel-ceklis th, .tabel-ceklis td { border: 1px solid black; padding: 10px; text-align: left; font-size: 15px; }
        .tabel-ceklis th { background-color: #f2f2f2; text-align: center; }
        
        .status-box { border: 2px dashed black; padding: 12px; text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 40px; background-color: #fafafa; }
        
        .ttd-area { width: 100%; margin-top: 30px; display: flex; justify-content: space-between; }
        .ttd-box { width: 40%; text-align: center; font-size: 16px; }
        .ttd-space { height: 80px; }
        
        @media print {
            body { padding: 0; }
            img { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="kop-container">
        <img class="kop-logo" src="Logo_Kecamatan.png" alt="Logo Kab HST">
        <div class="kop-teks">
            <h2>PEMERINTAH KABUPATEN HULU SUNGAI TENGAH</h2>
            <h3>KECAMATAN BIRAYANG</h3>
            <p>Alamat: Jl. Kesatria Kecamatan Birayang, Kode Pos 71351, Kalimantan Selatan</p>
        </div>
    </div>

    <div class="judul-surat">SURAT TANDA TERIMA BERKAS PERMOHONAN</div>

    <p>Telah diterima berkas persyaratan pengajuan Kartu Tanda Penduduk (KTP) melalui Sistem PATEN (Pelayanan Administrasi Terpadu Kecamatan) dari warga dengan identitas berikut:</p>

    <table class="tabel-data">
        <tr>
            <td width="35%">Nomor Registrasi</td>
            <td width="3%">:</td>
            <td><strong>REG-PATEN/KTP/00<?php echo intval($data['id']); ?></strong></td>
        </tr>
        <tr>
            <td>Nomor Induk Kependudukan (NIK)</td>
            <td>:</td>
            <td><?php echo htmlspecialchars($data['nik']); ?></td>
        </tr>
        <tr>
            <td>Nama Pemohon</td>
            <td>:</td>
            <td><strong><?php echo strtoupper(htmlspecialchars($data['nama'])); ?></strong></td>
        </tr>
        <tr>
            <td>Tanggal Penyerahan</td>
            <td>:</td>
            <td><?php echo tgl_indo($data['waktu_verifikasi']); ?></td>
        </tr>
    </table>

    <h4 style="margin-bottom: 10px;">Rincian Verifikasi Kelengkapan Berkas Fisik:</h4>
    
    <table class="tabel-ceklis">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Nama Berkas Persyaratan</th>
                <th width="25%">Status Kelengkapan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Fotokopi / Scan Kartu Keluarga (KK)</td>
                <td style="text-align: center; font-weight: bold;"><?php echo ($data['file_kk'] == 'Lengkap') ? 'LENGKAP' : 'BELUM LENGKAP'; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Surat Pengantar dari RT/RW Setempat</td>
                <td style="text-align: center; font-weight: bold;"><?php echo ($data['file_surat_pengantar'] == 'Lengkap') ? 'LENGKAP' : 'BELUM LENGKAP'; ?></td>
            </tr>
        </tbody>
    </table>

    <div class="status-box">
        KESIMPULAN STATUS BERKAS SAAT INI: <span style="text-decoration: underline;"><?php echo strtoupper(htmlspecialchars($data['status_berkas'])); ?></span>
    </div>

    <div class="ttd-area">
        <div class="ttd-box">
            <p>Pemohon/Warga</p>
            <div class="ttd-space"></div>
            <p>( ____________________ )</p>
        </div>
        <div class="ttd-box">
            <p>Birayang, <?php echo tgl_indo(date('Y-m-d')); ?></p>
            <p>Petugas Operator PATEN</p>
            <div class="ttd-space"></div>
            <p><strong>( Petugas Kecamatan )</strong></p>
        </div>
    </div>

</body>
</html>
<?php 
$conn->close(); 
?>