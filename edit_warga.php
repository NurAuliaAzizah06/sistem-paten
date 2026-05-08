<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
$conn = new mysqli('db', 'root', 'password_paten', 'db_paten');
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM warga WHERE id = $id");
$data = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $sql = "UPDATE warga SET nik='$nik', nama='$nama' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location='lihat_warga.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Data Warga - PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow mx-auto" style="max-width: 500px;">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Edit Data Warga</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control" value="<?php echo $data['nik']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo $data['nama']; ?>" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-warning w-100">Simpan Perubahan</button>
                    <a href="lihat_warga.php" class="btn btn-link w-100 mt-2 text-decoration-none text-muted">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>