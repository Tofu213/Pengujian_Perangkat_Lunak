<?php
session_start();
include "koneksi.php";

if (isset($_GET['kembali'])) {
    $id_pinjam = $_GET['kembali'];
    $id_admin = $_SESSION['id_user'];
    $tgl_aktual = date('Y-m-d'); // Tanggal dikembalikan (hari ini)
    
    // Ambil tanggal seharusnya buku dikembalikan
    $cek = mysqli_query($koneksi, "SELECT tgl_kembali_seharusnya, id_buku FROM peminjaman WHERE id_peminjaman = '$id_pinjam'");
    $data = mysqli_fetch_assoc($cek);
    $tgl_seharusnya = $data['tgl_kembali_seharusnya'];
    $id_buku = $data['id_buku'];

    // -------------------------------------------------------------
    // LOGIKA WHITE BOX TESTING (PERHITUNGAN DENDA)
    // -------------------------------------------------------------
    $selisih_detik = strtotime($tgl_aktual) - strtotime($tgl_seharusnya);
    $hari_terlambat = floor($selisih_detik / (60 * 60 * 24)); // Konversi detik ke hari

    // Path 1 & Path 2 Evaluasi
    if ($hari_terlambat > 0) {
        // Kondisi True: Terlambat
        $denda_per_hari = 2000; // Contoh tarif: Rp 2.000 / hari
        $total_denda = $hari_terlambat * $denda_per_hari;
    } else {
        // Kondisi False: Tepat waktu atau lebih cepat
        $hari_terlambat = 0;
        $total_denda = 0;
    }
    // -------------------------------------------------------------

    // Insert ke tabel pengembalian
    mysqli_query($koneksi, "INSERT INTO pengembalian (id_peminjaman, tgl_kembali_aktual, keterlambatan_hari, total_denda, id_admin) VALUES ('$id_pinjam', '$tgl_aktual', '$hari_terlambat', '$total_denda', '$id_admin')");
    
    // Update status peminjaman dan kembalikan stok buku
    mysqli_query($koneksi, "UPDATE peminjaman SET status_pinjam = 'Dikembalikan' WHERE id_peminjaman = '$id_pinjam'");
    mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");
    
    header("Location: pengembalian_buku.php");
}

$query = mysqli_query($koneksi, "SELECT p.*, m.nama_mahasiswa, b.judul FROM peminjaman p JOIN mahasiswa m ON p.nim = m.nim JOIN buku b ON p.id_buku = b.id_buku WHERE p.status_pinjam = 'Dipinjam'");
?>
<div class="container mt-5">
    <h4>Proses Pengembalian Buku</h4>
    <table class="table table-bordered bg-white mt-3">
        <thead><tr><th>Peminjam</th><th>Buku</th><th>Tgl Seharusnya Kembali</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= $row['nama_mahasiswa'] ?></td><td><?= $row['judul'] ?></td>
                <td class="text-danger fw-bold"><?= $row['tgl_kembali_seharusnya'] ?></td>
                <td><a href="?kembali=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-primary" onclick="return confirm('Proses pengembalian buku ini?')">Terima Buku</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>