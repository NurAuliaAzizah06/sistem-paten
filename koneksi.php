<?php
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    // Localhost - database dibuat otomatis
    $conn = new mysqli("localhost", "root", "", "");
    if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);
    $conn->query("CREATE DATABASE IF NOT EXISTS db_paten");
    $conn->select_db("db_paten");
} else {
    // InfinityFree - database sudah dibuat dari control panel
    $conn = new mysqli("ISI_HOST", "ISI_USERNAME", "ISI_PASSWORD", "ISI_NAMA_DATABASE");
    if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
