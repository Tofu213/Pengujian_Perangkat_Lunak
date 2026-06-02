<?php
// Baris ini HARUS diletakkan paling atas, tidak boleh ada spasi atau HTML sebelumnya
session_start();

// Validasi akses khusus mahasiswa
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";
$nim = $_SESSION['id_user'];

// Query untuk mengambil data peminjaman digabung dengan tabel buku dan pengembalian
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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_mahasiswa.php">SIP PERPUS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard_mahasiswa.php">Dashboard</a>
                <a class="nav-link" href="katalog_buku.php">Katalog Buku</a>
                <a class="nav-link active" href="riwayat_transaksi.php">Riwayat</a>
                <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3 fw-semibold" href="logout.php">Keluar</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark m-0">Riwayat Transaksi Peminjaman</h4>
        <a href="dashboard_mahasiswa.php" class="btn btn-outline-secondary btn-sm fw-semibold">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="card border-0 shadow-sm rounded">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-primary text-nowrap">
                        <tr>
                            <th class="p-3 text-center">No</th>
                            <th>Judul Buku</th>
                            <th class="text-center">Tanggal Pinjam</th>
                            <th class="text-center">Batas Kembali</th>
                            <th class="text-center">Tgl Kembali Aktual</th>
                            <th class="text-center">Status</th>
                            <th class="pe-3 text-end">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($result) > 0) : 
                            while ($row = mysqli_fetch_assoc($result)) : 
                        ?>
                                <tr>
                                    <td class="p-3 text-center"><?php echo $no++; ?></td>
                                    <td class="fw-semibold text-primary"><?php echo htmlspecialchars($row['judul']); ?></td>
                                    <td class="text-center"><?php echo date('d-m-Y', strtotime($row['tgl_pinjam'])); ?></td>
                                    <td class="text-center text-danger fw-semibold"><?php echo date('d-m-Y', strtotime($row['tgl_kembali_seharusnya'])); ?></td>
                                    <td class="text-center">
                                        <?php echo ($row['tgl_kembali_aktual']) ? date('d-m-Y', strtotime($row['tgl_kembali_aktual'])) : '<span class="text-muted">-</span>'; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        if ($row['status_pinjam'] == 'Menunggu Persetujuan') {
                                            echo '<span class="badge bg-warning text-dark shadow-sm">Menunggu Acc</span>';
                                        } elseif ($row['status_pinjam'] == 'Dipinjam') {
                                            echo '<span class="badge bg-success shadow-sm">Aktif (Dipinjam)</span>';
                                        } elseif ($row['status_pinjam'] == 'Ditolak') {
                                            echo '<span class="badge bg-danger shadow-sm">Ditolak</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary shadow-sm">Selesai Dikembalikan</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="pe-3 text-end fw-bold <?php echo (isset($row['total_denda']) && $row['total_denda'] > 0) ? 'text-danger' : 'text-muted'; ?>">
                                        <?php 
                                        $denda = $row['total_denda'] ?? 0;
                                        echo ($denda > 0) ? "Rp " . number_format($denda, 0, ',', '.') : '-';
                                        ?>
                                    </td>
                                </tr>
                        <?php 
                            endwhile; 
                        else : 
                        ?>
                            <tr>
                                <td colspan="7" class="text-center p-5 text-muted">
                                    <em>Belum ada riwayat transaksi peminjaman. Silakan pinjam buku di menu <strong>Katalog Buku</strong>.</em>
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
