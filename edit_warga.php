<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
$conn = new mysqli('db', 'root', 'bismillah123', 'db_paten');
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// PERUBAHAN 1: Mengubah pencarian dari 'id' menjadi 'nik' sesuai struktur tabel warga
if (isset($_GET['nik'])) {
    $nik_get = $conn->real_escape_string($_GET['nik']);
    
    // NIK adalah VARCHAR (string), jadi wajib dibungkus tanda petik tunggal '$nik_get'
    $result = $conn->query("SELECT * FROM warga WHERE nik = '$nik_get'");
    $data = $result->fetch_assoc();

    if (!$data) {
        die("<div class='container mt-5 alert alert-danger'>Data warga dengan NIK tersebut tidak ditemukan!</div>");
    }
} else {
    header("Location: lihat_warga.php");
    exit();
}

if (isset($_POST['update'])) {
    $nik_baru = $conn->real_escape_string($_POST['nik']);
    $nama = $conn->real_escape_string($_POST['nama']);
    
    // PERUBAHAN 2: Query update disesuaikan dengan WHERE nik = '$nik_get' (nik lama)
    $sql = "UPDATE warga SET nik='$nik_baru', nama='$nama' WHERE nik='$nik_get'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location='lihat_warga.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui data: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Warga - PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow mx-auto" style="max-width: 500px;">
            <div class="card-header bg-warning py-3">
                <h5 class="mb-0 fw-bold">✏️ Edit Data Warga</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIK</label>
                        <input type="text" name="nik" class="form-control" value="<?php echo htmlspecialchars($data['nik']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-warning w-100 fw-bold py-2">Simpan Perubahan</button>
                    <a href="lihat_warga.php" class="btn btn-link w-100 mt-2 text-decoration-none text-muted">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>