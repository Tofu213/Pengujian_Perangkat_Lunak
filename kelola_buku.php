<?php
session_start();
// Validasi akses admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Mengambil seluruh data buku dari database
$query = "SELECT * FROM buku ORDER BY id_buku DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Admin Perpus</title>
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
                <a class="nav-link active" href="kelola_buku.php">Kelola Buku</a>
                <a class="nav-link" href="kelola_peminjaman.php">Peminjaman</a>
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
            <h5 class="mb-0 fw-bold">Kelola Data Buku</h5>
            <div>
                <!-- Tombol Back / Kembali ke Dashboard -->
                <a href="dashboard_admin.php" class="btn btn-dark btn-sm fw-semibold me-2">
                    &larr; Kembali
                </a>
                <!-- Tombol Tambah Buku -->
                <a href="tambah_buku.php" class="btn btn-light btn-sm fw-semibold text-primary">
                    + Tambah Buku
                </a>
            </div>
        </div>
        
        <div class="card-body p-4">
            
            <!-- Notifikasi Pesan Berhasil/Gagal -->
            <?php if (isset($_GET['pesan'])) : ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <strong>Berhasil!</strong> <?php echo htmlspecialchars($_GET['pesan']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Tabel Data Buku -->
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle border">
                    <thead class="table-dark text-nowrap">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="15%">ISBN</th>
                            <th width="25%">Judul Buku</th>
                            <th width="20%">Pengarang</th>
                            <th width="15%">Penerbit</th>
                            <th class="text-center" width="10%">Stok</th>
                            <th class="text-center" width="10%">Aksi</th>
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
                            <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                            <td class="fw-semibold text-primary"><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td><?php echo htmlspecialchars($row['pengarang']); ?></td>
                            <td><?php echo htmlspecialchars($row['penerbit']); ?></td>
                            <td class="text-center fw-bold fs-5"><?php echo htmlspecialchars($row['stok']); ?></td>
                            <td class="text-center text-nowrap">
                                <a href="edit_buku.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-warning fw-semibold shadow-sm">Edit</a>
                                <a href="hapus_buku.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-danger fw-semibold shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini? Semua data terkait mungkin akan terhapus.');">Hapus</a>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <tr>
                            <td colspan="7" class="text-center p-5 text-muted">
                                <em>Belum ada data buku. Silakan klik tombol <strong>+ Tambah Buku</strong>.</em>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Memanggil JavaScript Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
