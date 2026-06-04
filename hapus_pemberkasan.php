<?php
// Koneksi database Docker kamu
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// Ambil ID dari URL tombol hapus
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Perbaikan: Kolom utama di database adalah 'id', bukan 'id_pemberkasan'
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
}

$conn->close();
?>