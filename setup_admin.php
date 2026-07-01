<?php
require_once 'koneksi.php';

// Membuat tabel admin
$sql = "CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL)";

if ($conn->query($sql) === TRUE) {
    
    // Menggunakan password_hash agar password tidak tersimpan sebagai teks biasa (lebih aman!)
    $user = 'adminpatenbry';
    $pass = password_hash('paten123bry', PASSWORD_DEFAULT);
    
    $check = $conn->query("SELECT * FROM admin WHERE username='$user'");
    if($check->num_rows == 0) {
        $conn->query("INSERT INTO admin (username, password) VALUES ('$user', '$pass')");
        echo "Tabel Admin siap dan akun default berhasil dibuat!";
    } else {
        echo "Tabel Admin sudah ada.";
    }
} else {
    echo "Gagal: " . $conn->error;
}
?>