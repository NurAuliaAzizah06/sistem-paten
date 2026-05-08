<?php
$conn = new mysqli('db', 'root', 'password_paten', 'db_paten');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM warga WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Data Berhasil Dihapus!'); window.location='lihat_warga.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>