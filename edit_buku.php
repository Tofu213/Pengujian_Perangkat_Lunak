<?php
session_start();
// Validasi akses admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Mendapatkan ID buku dari URL
if (!isset($_GET['id'])) {
    header("Location: kelola_buku.php");
    exit;
}

$id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);

// Mengambil data buku saat ini
$query_get = "SELECT * FROM buku WHERE id_buku = '$id_buku'";
$result_get = mysqli_query($koneksi, $query_get);

if (mysqli_num_rows($result_get) == 0) {
    header("Location: kelola_buku.php");
    exit;
}
$buku = mysqli_fetch_assoc($result_get);

// Logika ketika tombol update ditekan
if (isset($_POST['update'])) {
    $isbn      = mysqli_real_escape_string($koneksi, $_POST['isbn']);
    $judul     = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
    $penerbit  = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun     = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $stok      = (int)$_POST['stok'];

    $query_update = "UPDATE buku SET 
                        isbn = '$isbn', 
                        judul = '$judul', 
                        pengarang = '$pengarang', 
                        penerbit = '$penerbit', 
                        tahun_terbit = '$tahun', 
                        stok = '$stok' 
                     WHERE id_buku = '$id_buku'";
    
    if (mysqli_query($koneksi, $query_update)) {
        header("Location: kelola_buku.php?pesan=Buku berhasil diperbarui");
        exit;
    } else {
        $error = "Gagal memperbarui buku: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Admin Perpus</title>
    <!-- Memanggil CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0 fw-bold">Edit Data Buku</h5>
                    <!-- Tombol Back / Kembali -->
                    <a href="kelola_buku.php" class="btn btn-dark btn-sm fw-semibold">
                        &larr; Batal & Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="edit_buku.php?id=<?php echo $id_buku; ?>" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="isbn" class="form-label fw-semibold">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn" required value="<?php echo htmlspecialchars($buku['isbn']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="judul" class="form-label fw-semibold">Judul Buku</label>
                                <input type="text" class="form-control" id="judul" name="judul" required value="<?php echo htmlspecialchars($buku['judul']); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pengarang" class="form-label fw-semibold">Pengarang</label>
                                <input type="text" class="form-control" id="pengarang" name="pengarang" required value="<?php echo htmlspecialchars($buku['pengarang']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="penerbit" class="form-label fw-semibold">Penerbit</label>
                                <input type="text" class="form-control" id="penerbit" name="penerbit" required value="<?php echo htmlspecialchars($buku['penerbit']); ?>">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="tahun_terbit" class="form-label fw-semibold">Tahun Terbit</label>
                                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" required min="1900" max="2099" value="<?php echo htmlspecialchars($buku['tahun_terbit']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="stok" class="form-label fw-semibold">Jumlah Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" required min="0" value="<?php echo htmlspecialchars($buku['stok']); ?>">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="update" class="btn btn-warning fw-bold py-2">
                                Simpan Perubahan
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
