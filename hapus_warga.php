<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 1. Koneksi Database
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// 2. Ambil parameter NIK dari URL
if (isset($_GET['nik'])) {
    $nik = $conn->real_escape_string($_GET['nik']);

    // 3. Hapus File Fisik di Folder Uploads Terlebih Dahulu
    $sql_file = "SELECT file_surat_pengantar, file_kk FROM pemberkasan_ktp WHERE nik = '$nik'";
    $result_file = $conn->query($sql_file);

    if ($result_file && $result_file->num_rows > 0) {
        $row_file = $result_file->fetch_assoc();
        $target_dir = "uploads/";

        // Hapus file fisik Surat Pengantar
        if (!empty($row_file['file_surat_pengantar']) && file_exists($target_dir . $row_file['file_surat_pengantar'])) {
            unlink($target_dir . $row_file['file_surat_pengantar']);
        }

        // Hapus file fisik Kartu Keluarga (KK)
        if (!empty($row_file['file_kk']) && file_exists($target_dir . $row_file['file_kk'])) {
            unlink($target_dir . $row_file['file_kk']);
        }
    }

    // 4. Eksekusi Penghapusan Data Utama
    // Karena CASCADE aktif, menghapus di tabel 'warga' otomatis membersihkan tabel 'pemberkasan_ktp'
    $sql_warga = "DELETE FROM warga WHERE nik = '$nik'";
    
    if ($conn->query($sql_warga) === TRUE) {
        header("Location: lihat_warga.php");
        exit();
    } else {
        echo "Gagal menghapus data: " . $conn->error;
    }
} else {
    header("Location: lihat_warga.php");
    exit();
}

$conn->close();
?>