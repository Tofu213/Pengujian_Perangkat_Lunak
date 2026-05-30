<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit;
include "koneksi.php";
$buku = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id_buku DESC");
?>
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h4>Kelola Buku</h4>
        <a href="tambah_buku.php" class="btn btn-primary">+ Tambah Buku</a>
    </div>
    <table class="table table-bordered bg-white">
        <thead><tr><th>ISBN</th><th>Judul</th><th>Pengarang</th><th>Stok</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($buku)): ?>
            <tr>
                <td><?= $row['isbn'] ?></td><td><?= $row['judul'] ?></td><td><?= $row['pengarang'] ?></td><td><?= $row['stok'] ?></td>
                <td>
                    <a href="edit_buku.php?id=<?= $row['id_buku'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="hapus_buku.php?id=<?= $row['id_buku'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus buku?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>