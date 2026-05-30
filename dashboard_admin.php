<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }
include "koneksi.php";

$tot_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(stok) as total FROM buku"))['total'] ?? 0;
$tot_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(nim) as total FROM mahasiswa"))['total'] ?? 0;
$tot_pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(id_peminjaman) as total FROM peminjaman WHERE status_pinjam='Dipinjam'"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard_admin.php">PANEL ADMIN</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="kelola_buku.php">Kelola Buku</a>
                <a class="nav-link" href="kelola_peminjaman.php">Peminjaman</a>
                <a class="nav-link" href="pengembalian_buku.php">Pengembalian</a>
                <a class="nav-link" href="laporan.php">Laporan</a>
                <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3" href="logout.php">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h4>Dashboard Pustakawan</h4>
        <div class="row g-3 mt-2">
            <div class="col-md-4"><div class="card bg-primary text-white p-4"><h5>Total Stok Buku</h5><h2><?= $tot_buku ?></h2></div></div>
            <div class="col-md-4"><div class="card bg-success text-white p-4"><h5>Total Anggota</h5><h2><?= $tot_anggota ?></h2></div></div>
            <div class="col-md-4"><div class="card bg-warning text-dark p-4"><h5>Sedang Dipinjam</h5><h2><?= $tot_pinjam ?></h2></div></div>
        </div>
    </div>
</body>
</html>