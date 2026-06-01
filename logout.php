<?php
// Memulai session untuk mendapatkan akses ke session saat ini
session_start();

// Menghapus semua variabel session yang telah didaftarkan (seperti role, id_user, dll)
session_unset();

// Menghancurkan session secara keseluruhan dari server
session_destroy();

// Mengarahkan pengguna kembali ke halaman login setelah berhasil logout
header("Location: login.php");
exit;
?>
