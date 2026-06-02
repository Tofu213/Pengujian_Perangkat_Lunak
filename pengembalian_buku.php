<?php
session_start();
// Validasi akses admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

if (isset($_GET['kembali'])) {
    $id_pinjam = mysqli_real_escape_string($koneksi, $_GET['kembali']);
    $id_admin = $_SESSION['id_user'];
    $tgl_aktual = date('Y-m-d'); // Tanggal dikembalikan (hari ini)
    
    // Ambil tanggal seharusnya buku dikembalikan
    $cek = mysqli_query($koneksi, "SELECT tgl_kembali_seharusnya, id_buku FROM peminjaman WHERE id_peminjaman = '$id_pinjam'");
    $data = mysqli_fetch_assoc($cek);
    
    if($data) {
        $tgl_seharusnya = $data['tgl_kembali_seharusnya'];
        $id_buku = $data['id_buku'];

        // -------------------------------------------------------------
        // LOGIKA WHITE BOX TESTING (PERHITUNGAN DENDA)
        // -------------------------------------------------------------
        $selisih_detik = strtotime($tgl_aktual) - strtotime($tgl_seharusnya);
        $hari_terlambat = floor($selisih_detik / (60 * 60 * 24));

        if ($hari_terlambat > 0) {
            $denda_per_hari = 2000;
            $total_denda = $hari_terlambat * $denda_per_hari;
        } else {
            $hari_terlambat = 0;
            $total_denda = 0;
        }
        // -------------------------------------------------------------

        // Insert ke tabel pengembalian
        mysqli_query($koneksi, "INSERT INTO pengembalian (id_peminjaman, tgl_kembali_aktual, keterlambatan_hari, total_denda, id_admin) VALUES ('$id_pinjam', '$tgl_aktual', '$hari_terlambat', '$total_denda', '$id_admin')");
        
        // Update status peminjaman dan kembalikan stok buku
        mysqli_query($koneksi, "UPDATE peminjaman SET status_pinjam = 'Dikembalikan' WHERE id_peminjaman = '$id_pinjam'");
        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");
        
        header("Location: pengembalian_buku.php?pesan=Buku berhasil dikembalikan. Denda: Rp " . number_format($total_denda, 0, ',', '.'));
        exit;
    }
}

// Menampilkan data buku yang sedang dipinjam
$query = "SELECT p.*, m.nama_mahasiswa, b.judul 
          FROM peminjaman p 
          JOIN mahasiswa m ON p.nim = m.nim 
          JOIN buku b ON p.id_buku = b.id_buku 
          WHERE p.status_pinjam = 'Dipinjam'
          ORDER BY p.tgl_kembali_seharusnya ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - Admin Perpus</title>
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
                <a class="nav-link" href="kelola_peminjaman.php">Peminjaman</a>
                <a class="nav-link active" href="pengembalian_buku.php">Pengembalian</a>
                <a class="nav-link" href="laporan.php">Laporan</a>
                <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3 fw-semibold" href="logout.php">Keluar</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 fw-bold">Proses Pengembalian Buku</h5>
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
                            <th class="text-center">Tgl Seharusnya Kembali</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($result) > 0) :
                            while($row = mysqli_fetch_assoc($result)): 
                                // Cek status keterlambatan visual
                                $tgl_seharusnya = $row['tgl_kembali_seharusnya'];
                                $tgl_sekarang = date('Y-m-d');
                                $is_terlambat = (strtotime($tgl_sekarang) > strtotime($tgl_seharusnya));
                        ?>
                        <tr class="<?php echo $is_terlambat ? 'table-warning' : ''; ?>">
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td class="fw-semibold text-primary"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td class="text-center"><?php echo date('d-m-Y', strtotime($row['tgl_pinjam'])); ?></td>
                            <td class="text-center <?php echo $is_terlambat ? 'text-danger fw-bold' : 'fw-semibold'; ?>">
                                <?php echo date('d-m-Y', strtotime($tgl_seharusnya)); ?>
                                <?php if($is_terlambat) echo "<br><small>(Terlambat)</small>"; ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="?kembali=<?php echo $row['id_peminjaman']; ?>" class="btn btn-sm btn-primary fw-semibold shadow-sm" onclick="return confirm('Proses pengembalian buku ini?');">Terima Buku</a>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center p-5 text-muted">
                                <em>Tidak ada buku yang sedang dipinjam saat ini.</em>
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
