<?php
// Koneksi database Docker kamu
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// Ambil ID Pemberkasan dari URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Perbaikan: p.id_pemberkasan diganti menjadi p.id sesuai struktur tabel asli
    $query = $conn->query("SELECT p.*, w.nama FROM pemberkasan_ktp p 
                           JOIN warga w ON p.nik = w.nik 
                           WHERE p.id = $id");
    $data = $query->fetch_assoc();
    
    if (!$data) { die("Data tidak ditemukan!"); }
} else {
    header("Location: pemberkasan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima Berkas - <?php echo $data['nama']; ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 30px; line-height: 1.6; background-color: white; color: black; }
        
        /* LAYOUT BARU: KOP Surat dengan Logo di Kiri */
        .kop-container { display: flex; align-items: center; border-bottom: 3px double black; padding-bottom: 15px; margin-bottom: 30px; }
        .kop-logo { width: 80px; height: auto; padding-right: 20px; }
        .kop-teks { flex-grow: 1; text-align: center; padding-right: 60px; }
        
        .kop-teks h2 { margin: 0; text-transform: uppercase; font-size: 20px; letter-spacing: 0.5px; line-height: 1.2; }
        .kop-teks h3 { margin: 5px 0; font-size: 22px; font-weight: bold; line-height: 1.2; }
        .kop-teks p { margin: 0; font-size: 13px; font-style: italic; }
        
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

    <div class="judul-surat">SURAT TANDA TERIMA BERKAS PERMOHONAN KTP</div>

    <p>Telah diterima berkas persyaratan pengajuan Kartu Tanda Penduduk (KTP) melalui Sistem PATEN (Pelayanan Administrasi Terpadu Kecamatan) dari warga dengan identitas berikut:</p>

    <table class="tabel-data">
        <tr>
            <td width="30%">Nomor Registrasi</td>
            <td width="3%">:</td>
            <td><strong>REG-PATEN/KTP/00<?php echo $data['id']; ?></strong></td>
        </tr>
        <tr>
            <td>Nomor Induk Kependudukan (NIK)</td>
            <td>:</td>
            <td><?php echo $data['nik']; ?></td>
        </tr>
        <tr>
            <td>Nama Pemohon</td>
            <td>:</td>
            <td><strong><?php echo uppercase($data['nama']); ?></strong></td>
        </tr>
        <tr>
            <td>Tanggal Penyerahan</td>
            <td>:</td>
            <td><?php echo date('d F Y', strtotime($data['waktu_verifikasi'])); ?></td>
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
                <td style="text-align: center; font-weight: bold;"><?php echo (!empty($data['file_kk'])) ? 'LENGKAP' : 'BELUM LENGKAP'; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Surat Pengantar dari RT/RW Setempat</td>
                <td style="text-align: center; font-weight: bold;"><?php echo (!empty($data['file_surat_pengantar'])) ? 'LENGKAP' : 'BELUM LENGKAP'; ?></td>
            </tr>
        </tbody>
    </table>

    <div class="status-box">
        KESIMPULAN STATUS BERKAS SAAT INI: <span style="text-decoration: underline;"><?php echo uppercase($data['status_berkas']); ?></span>
    </div>

    <div class="ttd-area">
        <div class="ttd-box">
            <p>Pemohon/Warga</p>
            <div class="ttd-space"></div>
            <p>( ____________________ )</p>
        </div>
        <div class="ttd-box">
            <p>Birayang, <?php echo date('d F Y'); ?></p>
            <p>Petugas Operator PATEN</p>
            <div class="ttd-space"></div>
            <p><strong>( Petugas Kecamatan )</strong></p>
        </div>
    </div>

</body>
</html>
<?php 
$conn->close(); 

function uppercase($str) {
    return strtoupper($str);
}
?>