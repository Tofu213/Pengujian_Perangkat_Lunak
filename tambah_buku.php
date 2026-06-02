<?php
session_start();
// Validasi akses admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Logika ketika tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $isbn      = mysqli_real_escape_string($koneksi, $_POST['isbn']);
    $judul     = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
    $penerbit  = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun     = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $stok      = (int)$_POST['stok'];

    $query = "INSERT INTO buku (isbn, judul, pengarang, penerbit, tahun_terbit, stok) 
              VALUES ('$isbn', '$judul', '$pengarang', '$penerbit', '$tahun', '$stok')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: kelola_buku.php?pesan=Buku berhasil ditambahkan");
        exit;
    } else {
        $error = "Gagal menambah buku: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Admin Perpus</title>
    <!-- Memanggil CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0 fw-bold">Tambah Data Buku</h5>
                    <!-- Tombol Back / Kembali -->
                    <a href="kelola_buku.php" class="btn btn-light btn-sm fw-semibold">
                        &larr; Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="tambah_buku.php" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="isbn" class="form-label fw-semibold">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn" required placeholder="Contoh: 978-602-...">
                            </div>
                            <div class="col-md-6">
                                <label for="judul" class="form-label fw-semibold">Judul Buku</label>
                                <input type="text" class="form-control" id="judul" name="judul" required placeholder="Masukkan judul buku">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pengarang" class="form-label fw-semibold">Pengarang</label>
                                <input type="text" class="form-control" id="pengarang" name="pengarang" required placeholder="Nama pengarang">
                            </div>
                            <div class="col-md-6">
                                <label for="penerbit" class="form-label fw-semibold">Penerbit</label>
                                <input type="text" class="form-control" id="penerbit" name="penerbit" required placeholder="Nama penerbit">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="tahun_terbit" class="form-label fw-semibold">Tahun Terbit</label>
                                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" required min="1900" max="2099" placeholder="Contoh: 2023">
                            </div>
                            <div class="col-md-6">
                                <label for="stok" class="form-label fw-semibold">Jumlah Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" required min="1" placeholder="Masukkan jumlah stok">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="simpan" class="btn btn-success fw-bold py-2">
                                Simpan Data Buku
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
