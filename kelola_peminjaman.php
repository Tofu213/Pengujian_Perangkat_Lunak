<?php
session_start();
// Validasi akses admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Logika ACC Peminjaman
if (isset($_GET['acc'])) {
    $id_pinjam = mysqli_real_escape_string($koneksi, $_GET['acc']);
    
    // Kurangi stok buku 1
    mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = (SELECT id_buku FROM peminjaman WHERE id_peminjaman = '$id_pinjam')");
    // Ubah status
    mysqli_query($koneksi, "UPDATE peminjaman SET status_pinjam = 'Dipinjam' WHERE id_peminjaman = '$id_pinjam'");
    
    header("Location: kelola_peminjaman.php?pesan=Peminjaman berhasil disetujui.");
    exit;
}

// Mengambil data peminjaman yang menunggu persetujuan
$query = "SELECT p.*, m.nama_mahasiswa, b.judul 
          FROM peminjaman p 
          JOIN mahasiswa m ON p.nim = m.nim 
          JOIN buku b ON p.id_buku = b.id_buku 
          WHERE p.status_pinjam = 'Menunggu Persetujuan' 
          ORDER BY p.id_peminjaman DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - Admin Perpus</title>
    <!-- Memanggil CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar Admin -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_admin.php">PANEL ADMIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard_admin.php">Dashboard</a>
                <a class="nav-link" href="kelola_buku.php">Kelola Buku</a>
                <a class="nav-link active" href="kelola_peminjaman.php">Peminjaman</a>
                <a class="nav-link" href="pengembalian_buku.php">Pengembalian</a>
                <a class="nav-link" href="laporan.php">Laporan</a>
                <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3 fw-semibold" href="logout.php">Keluar</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 fw-bold">Verifikasi Pengajuan Peminjaman</h5>
            <div>
                <!-- Tombol Back / Kembali ke Dashboard -->
                <a href="dashboard_admin.php" class="btn btn-dark btn-sm fw-semibold">
                    &larr; Kembali
                </a>
            </div>
        </div>
        
        <div class="card-body p-4">
            
            <?php if (isset($_GET['pesan'])) : ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <strong>Berhasil!</strong> <?php echo htmlspecialchars($_GET['pesan']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle border">
                    <thead class="table-dark text-nowrap">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Peminjam</th>
                            <th>Judul Buku</th>
                            <th class="text-center">Tanggal Pinjam</th>
                            <th class="text-center">Batas Kembali</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($result) > 0) :
                            while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td class="fw-semibold text-primary"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td class="text-center"><?php echo date('d-m-Y', strtotime($row['tgl_pinjam'])); ?></td>
                            <td class="text-center text-danger fw-semibold"><?php echo date('d-m-Y', strtotime($row['tgl_kembali_seharusnya'])); ?></td>
                            <td class="text-center text-nowrap">
                                <a href="?acc=<?php echo $row['id_peminjaman']; ?>" class="btn btn-sm btn-success fw-semibold shadow-sm" onclick="return confirm('Setujui pengajuan peminjaman ini?');">Setujui</a>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center p-5 text-muted">
                                <em>Tidak ada pengajuan peminjaman yang menunggu persetujuan.</em>
                            </td>
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
