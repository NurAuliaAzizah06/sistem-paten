<?php
// 1. Buka Koneksi Database
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 2. Tangkap data teks dari form tambah_warga.php
$nik = $conn->real_escape_string($_POST['nik']);
$nama = $conn->real_escape_string($_POST['nama']);
$status_berkas = $conn->real_escape_string($_POST['status_berkas']);

// 3. Persiapan Folder Upload
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true); 
}

// 4. Proses Eksekusi Upload Surat Pengantar
$nama_file_sp = "";
if (isset($_FILES['file_surat_pengantar']) && $_FILES['file_surat_pengantar']['error'] == 0) {
    $ext_sp = pathinfo($_FILES['file_surat_pengantar']['name'], PATHINFO_EXTENSION);
    $nama_file_sp = $nik . "_SP_" . time() . "." . $ext_sp; 
    move_uploaded_file($_FILES['file_surat_pengantar']['tmp_name'], $target_dir . $nama_file_sp);
}

// 5. Proses Eksekusi Upload Kartu Keluarga (KK)
$nama_file_kk = "";
if (isset($_FILES['file_kk']) && $_FILES['file_kk']['error'] == 0) {
    $ext_kk = pathinfo($_FILES['file_kk']['name'], PATHINFO_EXTENSION);
    $nama_file_kk = $nik . "_KK_" . time() . "." . $ext_kk; 
    move_uploaded_file($_FILES['file_kk']['tmp_name'], $target_dir . $nama_file_kk);
}

// 6. Masukkan Data ke Database (Dua Tabel Sekaligus)

// PERBAIKAN: Kolom status_berkas sekarang ikut dimasukkan ke tabel 'warga'
$sql_warga = "INSERT INTO warga (nik, nama, status_berkas) VALUES ('$nik', '$nama', '$status_berkas') 
              ON DUPLICATE KEY UPDATE nama='$nama', status_berkas='$status_berkas'";
$conn->query($sql_warga);

// Simpan data file fisik ke tabel 'pemberkasan_ktp'
$sql_berkas = "INSERT INTO pemberkasan_ktp (nik, status_berkas, file_surat_pengantar, file_kk) 
               VALUES ('$nik', '$status_berkas', '$nama_file_sp', '$nama_file_kk')
               ON DUPLICATE KEY UPDATE status_berkas='$status_berkas', file_surat_pengantar='$nama_file_sp', file_kk='$nama_file_kk'";

// 7. Cek apakah berhasil
if ($conn->query($sql_berkas) === TRUE) {
    // Kalau sukses, otomatis arahkan kembali ke halaman lihat warga
    header("Location: lihat_warga.php");
    exit();
} else {
    // Kalau gagal, tampilkan pesan errornya
    echo "Gagal menyimpan data pemberkasan: " . $conn->error;
}

$conn->close();
?>