<?php
session_start();
// Validasi akses admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$query = "SELECT p.tgl_pinjam, pg.tgl_kembali_aktual, b.judul, m.nama_mahasiswa, pg.keterlambatan_hari, pg.total_denda 
          FROM pengembalian pg 
          JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman 
          JOIN buku b ON p.id_buku = b.id_buku 
          JOIN mahasiswa m ON p.nim = m.nim 
          ORDER BY pg.id_pengembalian DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Admin Perpus</title>
    <!-- Memanggil CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sembunyikan elemen tertentu saat mode cetak (print) */
        @media print {
            .no-print {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-light">

<!-- Navbar Admin (Disembunyikan saat dicetak) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm no-print">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_admin.php">PANEL ADMIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard_admin.php">Dashboard</a>
                <a class="nav-link" href="kelola_buku.php">Kelola Buku</a>
                <a class="nav-link" href="kelola_peminjaman.php">Peminjaman</a>
                <a class="nav-link" href="pengembalian_buku.php">Pengembalian</a>
                <a class="nav-link active" href="laporan.php">Laporan</a>
                <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3 fw-semibold" href="logout.php">Keluar</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3 no-print">
            <h5 class="mb-0 fw-bold">Rekapitulasi Laporan Transaksi</h5>
            <div>
                <!-- Tombol Back / Kembali ke Dashboard -->
                <a href="dashboard_admin.php" class="btn btn-dark btn-sm fw-semibold me-2">
                    &larr; Kembali
                </a>
                <!-- Tombol Cetak -->
                <button onclick="window.print()" class="btn btn-light btn-sm fw-semibold text-primary">
                    &#128424; Cetak Laporan
                </button>
            </div>
        </div>
        
        <div class="card-body p-4">
            
            <div class="text-center mb-4 d-none d-print-block">
                <h4 class="fw-bold">LAPORAN TRANSAKSI PERPUSTAKAAN</h4>
                <p class="mb-0">Universitas Pembangunan Jaya</p>
                <hr>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle border">
                    <thead class="table-dark text-nowrap">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Peminjam</th>
                            <th>Judul Buku</th>
                            <th class="text-center">Tanggal Pinjam</th>
                            <th class="text-center">Tanggal Kembali</th>
                            <th class="text-center">Terlambat</th>
                            <th class="text-end">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $total_semua_denda = 0;
                        if (mysqli_num_rows($result) > 0) :
                            while($row = mysqli_fetch_assoc($result)): 
                                $total_semua_denda += $row['total_denda'];
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td class="fw-semibold text-primary"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td class="text-center"><?php echo date('d-m-Y', strtotime($row['tgl_pinjam'])); ?></td>
                            <td class="text-center"><?php echo date('d-m-Y', strtotime($row['tgl_kembali_aktual'])); ?></td>
                            <td class="text-center">
                                <?php echo ($row['keterlambatan_hari'] > 0) ? $row['keterlambatan_hari']." Hari" : "-"; ?>
                            </td>
                            <td class="text-end fw-bold <?php echo ($row['total_denda'] > 0) ? 'text-danger' : 'text-muted'; ?>">
                                Rp <?php echo number_format($row['total_denda'], 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <tr>
                            <td colspan="7" class="text-center p-5 text-muted">
                                <em>Belum ada transaksi pengembalian yang diselesaikan.</em>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (mysqli_num_rows($result) > 0) : ?>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-bold fs-5">TOTAL PENDAPATAN DENDA :</td>
                            <td class="text-end fw-bold text-danger fs-5">Rp <?php echo number_format($total_semua_denda, 0, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
