<?php
session_start();
include "koneksi.php";

// Logika ACC Peminjaman
if (isset($_GET['acc'])) {
    $id_pinjam = $_GET['acc'];
    // Kurangi stok buku 1
    mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = (SELECT id_buku FROM peminjaman WHERE id_peminjaman = '$id_pinjam')");
    // Ubah status
    mysqli_query($koneksi, "UPDATE peminjaman SET status_pinjam = 'Dipinjam' WHERE id_peminjaman = '$id_pinjam'");
    header("Location: kelola_peminjaman.php");
}

$query = mysqli_query($koneksi, "SELECT p.*, m.nama_mahasiswa, b.judul FROM peminjaman p JOIN mahasiswa m ON p.nim = m.nim JOIN buku b ON p.id_buku = b.id_buku WHERE p.status_pinjam = 'Menunggu Persetujuan'");
?>
<div class="container mt-5">
    <h4>Verifikasi Peminjaman</h4>
    <table class="table table-striped bg-white mt-3">
        <thead><tr><th>Peminjam</th><th>Buku</th><th>Tgl Pinjam</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= $row['nama_mahasiswa'] ?></td><td><?= $row['judul'] ?></td><td><?= $row['tgl_pinjam'] ?></td>
                <td><a href="?acc=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-success">Setujui</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>