<?php
// 1. Proteksi Session Keamanan Area Admin
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 2. Koneksi database
require_once 'koneksi.php';

// 3. Ambil ID dari URL tombol hapus
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Perbaikan mandiri kamu sudah sangat tepat: menggunakan kolom 'id'
    $sql_delete = "DELETE FROM pemberkasan_ktp WHERE id = $id";
    
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>
                alert('Data riwayat pemberkasan berhasil dihapus!');
                window.location.href='pemberkasan.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus data: " . $conn->error . "');
                window.location.href='pemberkasan.php';
              </script>";
    }
} else {
    header("Location: pemberkasan.php");
    exit(); // Ditambahkan exit agar eksekusi script langsung berhenti setelah redirect
}

$conn->close();
?>