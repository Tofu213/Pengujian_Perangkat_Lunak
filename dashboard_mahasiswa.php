<?php
session_start();
// Proteksi halaman: pastikan pengguna sudah login dan perannya adalah mahasiswa
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";
$nim = $_SESSION['id_user'];
$nama = $_SESSION['nama_user'];

// 1. Hitung total buku yang sedang dipinjam
$query_dipinjam = "SELECT COUNT(*) AS total FROM peminjaman WHERE nim = '$nim' AND status_pinjam = 'Dipinjam'";
$res_dipinjam = mysqli_query($koneksi, $query_dipinjam);
$data_dipinjam = mysqli_fetch_assoc($res_dipinjam);

// 2. Hitung total pengajuan yang menunggu persetujuan admin
$query_tunggu = "SELECT COUNT(*) AS total FROM peminjaman WHERE nim = '$nim' AND status_pinjam = 'Menunggu Persetujuan'";
$res_tunggu = mysqli_query($koneksi, $query_tunggu);
$data_tunggu = mysqli_fetch_assoc($res_tunggu);

// 3. Hitung total denda yang belum dibayar (jika ada transaksi pengembalian)
$query_denda = "SELECT SUM(pg.total_denda) AS total_denda FROM pengembalian pg 
                JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman 
                WHERE p.nim = '$nim'";
$res_denda = mysqli_query($koneksi, $query_denda);
$data_denda = mysqli_fetch_assoc($res_denda);
$total_denda = $data_denda['total_denda'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - SIP Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_mahasiswa.php">SIP PERPUS</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="dashboard_mahasiswa.php">Dashboard</a>
            <a class="nav-link" href="katalog_buku.php">Katalog Buku</a>
            <a class="nav-link" href="riwayat_transaksi.php">Riwayat</a>
            <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3" href="logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col">
            <div class="p-4 bg-white rounded shadow-sm">
                <h4>Selamat Datang, <span class="text-primary fw-bold"><?php echo htmlspecialchars($nama); ?></span>!</h4>
                <p class="text-muted mb-0">NIM: <?php echo htmlspecialchars($nim); ?> | Panel Anggota Perpustakaan</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body p-4">
                    <h6 class="card-title text-uppercase text-white-50 small font-weight-bold">Buku Dipinjam</h6>
                    <h2 class="display-6 fw-bold m-0"><?php echo $data_dipinjam['total']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body p-4">
                    <h6 class="card-title text-uppercase text-white-50 small font-weight-bold">Menunggu Persetujuan</h6>
                    <h2 class="display-6 fw-bold m-0"><?php echo $data_tunggu['total']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body p-4">
                    <h6 class="card-title text-uppercase text-white-50 small font-weight-bold">Total Denda Akumulatif</h6>
                    <h2 class="display-6 fw-bold m-0">Rp <?php echo number_format($total_denda, 0, ',', '.'); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>