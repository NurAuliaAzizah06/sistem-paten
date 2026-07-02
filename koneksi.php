<?php
if (getenv('APP_ENV') === 'docker') {
    // Docker Desktop - MySQL ada di container 'db'
    $conn = new mysqli("db", "root", "bismillah123", "db_paten");
    if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);
} elseif (getenv('MYSQLHOST') || getenv('DB_HOST')) {
    // Railway / Production - env vars diset di Railway dashboard
    $host = getenv('MYSQLHOST') ?: getenv('DB_HOST');
    $user = getenv('MYSQLUSER') ?: getenv('DB_USER');
    $pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD');
    $name = getenv('MYSQLDATABASE') ?: getenv('DB_NAME');
    $port = (int)(getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306);
    $conn = new mysqli($host, $user, $pass, $name, $port);
    if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);
} elseif ($_SERVER['HTTP_HOST'] === 'localhost' || str_starts_with($_SERVER['HTTP_HOST'], 'localhost:') || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    // Localhost Laragon - database dibuat otomatis
    $conn = new mysqli("localhost", "root", "", "");
    if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);
    $conn->query("CREATE DATABASE IF NOT EXISTS db_paten");
    $conn->select_db("db_paten");
} else {
    // Hosting lain (InfinityFree, cPanel, dll)
    $conn = new mysqli(
        getenv('DB_HOST') ?: 'ISI_HOST',
        getenv('DB_USER') ?: 'ISI_USERNAME',
        getenv('DB_PASSWORD') ?: 'ISI_PASSWORD',
        getenv('DB_NAME') ?: 'ISI_NAMA_DATABASE'
    );
    if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
