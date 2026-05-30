<?php
// Memulai session untuk menyimpan data login pengguna
session_start();

// Menghubungkan ke file koneksi database
include "koneksi.php";

// Variabel untuk menyimpan pesan kesalahan
$error = "";

// Memeriksa apakah tombol login telah diklik
if (isset($_POST['login'])) {
    // Mengamankan input dari SQL Injection
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; 
    $role     = $_POST['role'];

    // Validasi input tidak boleh kosong
    if (empty($username) || empty($password) || empty($role)) {
        $error = "Semua kolom harus diisi!";
    } else {
        if ($role == "admin") {
            // Query untuk memeriksa data di tabel admin
            $query = "SELECT * FROM admin WHERE username = '$username'";
            $result = mysqli_query($koneksi, $query);

            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                
                // Verifikasi password (ganti dengan password_verify() jika dienkripsi)
                if ($password === $row['password']) {
                    // Menyimpan data ke dalam session
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['id_user']      = $row['id_admin'];
                    $_SESSION['username']     = $row['username'];
                    $_SESSION['nama_user']    = $row['nama_admin'];
                    $_SESSION['role']         = "admin";

                    // Dialihkan ke dashboard admin
                    header("Location: dashboard_admin.php");
                    exit;
                }
            }
        } elseif ($role == "mahasiswa") {
            // Query untuk memeriksa data di tabel mahasiswa yang statusnya aktif
            $query = "SELECT * FROM mahasiswa WHERE username = '$username' AND status_aktif = 'Aktif'";
            $result = mysqli_query($koneksi, $query);

            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);

                if ($password === $row['password']) {
                    // Menyimpan data ke dalam session
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['id_user']      = $row['nim'];
                    $_SESSION['username']     = $row['username'];
                    $_SESSION['nama_user']    = $row['nama_mahasiswa'];
                    $_SESSION['role']         = "mahasiswa";

                    // Dialihkan ke dashboard mahasiswa
                    header("Location: dashboard_mahasiswa.php");
                    exit;
                }
            }
        }
        // Jika tidak ada kecocokan data
        $error = "Username, password, atau hak akses salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/style.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            margin-top: 10%;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center login-container">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px; border-radius: 10px;">
        <div class="card-body">
            <h4 class="card-title text-center mb-4 fw-bold text-primary">SIP PERPUSTAKAAN</h4>
            <p class="text-muted text-center small mb-4">Silakan masuk menggunakan akun Anda</p>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger text-center small p-2" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label small fw-semibold">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <div class="mb-4">
                    <label for="role" class="form-label small fw-semibold">Masuk Sebagai</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="" disabled selected>Pilih hak akses...</option>
                        <option value="mahasiswa">Mahasiswa (Anggota)</option>
                        <option value="admin">Pustakawan (Admin)</option>
                    </select>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 fw-semibold" style="border-radius: 5px;">Masuk</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>