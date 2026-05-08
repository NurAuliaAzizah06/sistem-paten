<?php
// JURUS PAMUNGKAS: Paksa waktu ke Makassar
date_default_timezone_set('Asia/Makassar');

$conn = new mysqli('db', 'root', 'password_paten', 'db_paten');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    
    // Kita ambil jam sekarang sesuai Asia/Makassar
    $waktu_sekarang = date('Y-m-d H:i:s');

    // Kita masukkan jamnya manual ke kolom tgl_input
    $sql = "INSERT INTO warga (nik, nama, tgl_input) VALUES ('$nik', '$nama', '$waktu_sekarang')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Data Berhasil Disimpan!'); window.location='lihat_warga.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>