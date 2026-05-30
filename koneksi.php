<?php
// Konfigurasi Database
$host     = "localhost";
$username = "root";
$password = ""; // Kosongkan jika menggunakan XAMPP bawaan
$database = "perpus_upj";

// Membuat koneksi ke MySQL
$koneksi = mysqli_connect($host, $username, $password, $database);

// Memeriksa apakah koneksi berhasil atau gagal
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8 untuk memastikan penanganan karakter aman
mysqli_set_charset($koneksi, "utf8");
