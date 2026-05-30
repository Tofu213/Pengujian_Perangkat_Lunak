<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

if (isset($_POST['pinjam'])) {
    $nim     = $_SESSION['id_user'];
    $id_buku = mysqli_real_escape_string($koneksi, $_POST['id_buku']);
    
    // 1. Cek ketersediaan stok buku terlebih dahulu
    $cek_buku = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = '$id_buku'");
    $data_buku = mysqli_fetch_assoc($cek_buku);
    
    if ($data_buku['stok'] > 0) {
        // Tentukan tanggal pinjam (hari ini) dan batas pengembalian (misal: default 7 hari ke depan)
        $tgl_pinjam = date('Y-m-d');
        $tgl_kembali_seharusnya = date('Y-m-d', strtotime('+7 days'));
        
        // 2. Insert data transaksi dengan status default 'Menunggu Persetujuan'
        $query_insert = "INSERT INTO peminjaman (nim, id_buku, tgl_pinjam, tgl_kembali_seharusnya, status_pinjam) 
                         VALUES ('$nim', '$id_buku', '$tgl_pinjam', '$tgl_kembali_seharusnya', 'Menunggu Persetujuan')";
        
        if (mysqli_query($koneksi, $query_insert)) {
            // Berhasil mengajukan pinjaman
            header("Location: katalog_buku.php?pesan=Pengajuan peminjaman berhasil dikirim. Menunggu konfirmasi pustakawan.");
            exit;
        } else {
            header("Location: katalog_buku.php?pesan=Gagal memproses pengajuan transaksi.");
            exit;
        }
    } else {
        header("Location: katalog_buku.php?pesan=Maaf, stok buku sudah habis.");
        exit;
    }
} else {
    // Jika diakses secara ilegal tanpa POST
    header("Location: katalog_buku.php");
    exit;
}
?>