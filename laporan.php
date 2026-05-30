<?php
session_start();
include "koneksi.php";

$query = mysqli_query($koneksi, "SELECT p.tgl_pinjam, pg.tgl_kembali_aktual, b.judul, m.nama_mahasiswa, pg.keterlambatan_hari, pg.total_denda 
                                 FROM pengembalian pg 
                                 JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman 
                                 JOIN buku b ON p.id_buku = b.id_buku 
                                 JOIN mahasiswa m ON p.nim = m.nim 
                                 ORDER BY pg.id_pengembalian DESC");
?>
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h4>Laporan Transaksi Perpustakaan</h4>
        <button onclick="window.print()" class="btn btn-secondary">Cetak PDF</button>
    </div>
    <table class="table table-striped bg-white border">
        <thead class="table-dark">
            <tr><th>Peminjam</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Terlambat (Hari)</th><th>Denda</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= $row['nama_mahasiswa'] ?></td><td><?= $row['judul'] ?></td>
                <td><?= $row['tgl_pinjam'] ?></td><td><?= $row['tgl_kembali_aktual'] ?></td>
                <td><?= $row['keterlambatan_hari'] ?></td>
                <td class="text-danger fw-bold">Rp <?= number_format($row['total_denda'],0,',','.') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>