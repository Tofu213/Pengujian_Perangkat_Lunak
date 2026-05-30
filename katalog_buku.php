<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Logika Pencarian
$search = "";
$query_sql = "SELECT * FROM buku";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $query_sql .= " WHERE judul LIKE '%$search%' OR pengarang LIKE '%$search%' OR isbn LIKE '%$search%'";
}
$result = mysqli_query($koneksi, $query_sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - SIP Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_mahasiswa.php">SIP PERPUS</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard_mahasiswa.php">Dashboard</a>
            <a class="nav-link active" href="katalog_buku.php">Katalog Buku</a>
            <a class="nav-link" href="riwayat_transaksi.php">Riwayat</a>
            <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3" href="logout.php">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark m-0">Katalog Buku</h3>
        <form action="katalog_buku.php" method="GET" class="d-flex style='width: 300px;'">
            <input class="form-control me-2" type="search" name="search" placeholder="Cari judul/pengarang..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
        </form>
    </div>

    <?php if (isset($_GET['pesan'])) : ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['pesan']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php while ($buku = mysqli_fetch_assoc($result)) : ?>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-primary text-truncate" title="<?php echo $buku['judul']; ?>">
                                <?php echo $buku['judul']; ?>
                            </h5>
                            <p class="card-text text-muted small mb-1">Pengarang: <?php echo $buku['pengarang']; ?></p>
                            <p class="card-text text-muted small mb-3">Stok Tersedia: <strong><?php echo $buku['stok']; ?></strong></p>
                            
                            <div class="mt-auto">
                                <?php if ($buku['stok'] > 0) : ?>
                                    <form action="proses_pinjam.php" method="POST">
                                        <input type="hidden" name="id_buku" value="<?php echo $buku['id_buku']; ?>">
                                        <button type="submit" name="pinjam" class="btn btn-primary btn-sm w-100 fw-semibold">Pinjam Buku</button>
                                    </form>
                                <?php else : ?>
                                    <button class="btn btn-secondary btn-sm w-100" disabled>Stok Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="col-12 text-center my-5">
                <p class="text-muted">Buku tidak ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>