<?php require_once 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Data Warga - PATEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container mt-5">
        <h2 class="text-center">LAPORAN DATA KEPENDUDUKAN</h2>
        <h4 class="text-center">KECAMATAN BIRAYANG</h4>
        <hr>
        
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Warga</th>
                    <th>Waktu Terdaftar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $query = "SELECT * FROM warga ORDER BY waktu_input DESC";
                $result = $conn->query($query);
                while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['nik']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['waktu_input']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="mt-5 text-end">
            <p>Birayang, <?php echo date('d F Y'); ?></p>
            <br><br>
            <p><strong>( Admin PATEN )</strong></p>
        </div>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary">Cetak Lagi</button>
            <a href="lihat_warga.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</body>
</html>