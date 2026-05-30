<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";
$nim = $_SESSION['id_user'];

// Melakukan JOIN antar tabel untuk menarik informasi judul buku dan denda dari relasi tabel pengembalian
$query_sql = "SELECT p.*, b.judul, pg.tgl_kembali_aktual, pg.total_denda 
              FROM peminjaman p 
              JOIN buku b ON p.id_buku = b.id_buku 
              LEFT JOIN pengembalian pg ON p.id_peminjaman = pg.id_peminjaman 
              WHERE p.nim = '$nim' 
              ORDER BY p.id_peminjaman DESC";
$result = mysqli_query($koneksi, $query_sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - SIP Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_mahasiswa.php">SIP PERPUS</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard_mahasiswa.php">Dashboard</a>
            <a class="nav-link" href="katalog_buku.php">Katalog Buku</a>
            <a class="nav-link active" href="riwayat_transaksi.php">Riwayat</a>
            <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3" href="logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="fw-bold text-dark mb-4">Riwayat Transaksi Peminjaman</h3>

    <div class="card border-0 shadow-sm rounded">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-primary text-nowrap">
                        <tr>
                            <th class="p-3">Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Tanggal Kembali Aktual</th>
                            <th>Status</th>
                            <th class="pe-3 text-end">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td class="p-3 fw-semibold"><?php echo htmlspecialchars($row['judul']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['tgl_pinjam'])); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['tgl_kembali_seharusnya'])); ?></td>
                                    <td>
                                        <?php echo ($row['tgl_kembali_aktual']) ? date('d-m-Y', strtotime($row['tgl_kembali_aktual'])) : '-'; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($row['status_pinjam'] == 'Menunggu Persetujuan') {
                                            echo '<span class="badge bg-warning text-dark">Menunggu Acc</span>';
                                        } elseif ($row['status_pinjam'] == 'Dipinjam') {
                                            echo '<span class="badge bg-success">Aktif (Dipinjam)</span>';
                                        } elseif ($row['status_pinjam'] == 'Ditolak') {
                                            echo '<span class="badge bg-danger">Ditolak</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">Selesai Dikembalikan</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="pe-3 text-end fw-bold text-danger">
                                        <?php 
                                        $denda = $row['total_denda'] ?? 0;
                                        echo ($denda > 0) ? "Rp " . number_format($denda, 0, ',', '.') : '-';
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center p-4 text-muted">Belum ada riwayat transaksi peminjaman.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>