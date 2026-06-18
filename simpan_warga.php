<?php
session_start();
// Proteksi halaman: pastikan hanya admin yang bisa mengeksekusi script ini
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 1. Buka Koneksi Database (Menggunakan konfigurasi container Docker-mu)
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// 2. Tangkap data teks dari form tambah_warga.php
$nik = $conn->real_escape_string($_POST['nik']);
$nama = $conn->real_escape_string($_POST['nama']);
$status_berkas = $conn->real_escape_string($_POST['status_berkas']);

// 3. Persiapan Folder Upload
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    // Menggunakan permission 0777 agar aman dari isu restriction di lingkungan Docker/Linux local
    mkdir($target_dir, 0777, true); 
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

// 6. Masukkan Data ke Database (Dua Tabel Sekaligus dengan Validasi Berantai)

$sql_warga = "INSERT INTO warga (nik, nama, status_berkas) VALUES ('$nik', '$nama', '$status_berkas') 
              ON DUPLICATE KEY UPDATE nama='$nama', status_berkas='$status_berkas'";

// Jalankan query pertama (Tabel Warga)
if ($conn->query($sql_warga) === TRUE) {
    
    // Jika tabel warga sukses, jalankan query kedua (Tabel Pemberkasan KTP)
    $sql_berkas = "INSERT INTO pemberkasan_ktp (nik, status_berkas, file_surat_pengantar, file_kk) 
                   VALUES ('$nik', '$status_berkas', '$nama_file_sp', '$nama_file_kk')
                   ON DUPLICATE KEY UPDATE status_berkas='$status_berkas', file_surat_pengantar='$nama_file_sp', file_kk='$nama_file_kk'";

    if ($conn->query($sql_berkas) === TRUE) {
        // Jika keduanya sukses, tampilkan alert lalu redirect ke lihat_warga.php
        echo "<script>
                alert('Data Warga dan File Berkas Berhasil Disimpan!');
                window.location.href = 'lihat_warga.php';
              </script>";
        exit();
    } else {
        // Jika tabel berkas gagal
        echo "Gagal menyimpan data pada tabel pemberkasan_ktp: " . $conn->error;
    }

} else {
    // Jika tabel warga gagal
    echo "Gagal menyimpan data pada tabel warga: " . $conn->error;
}

$conn->close();
?>