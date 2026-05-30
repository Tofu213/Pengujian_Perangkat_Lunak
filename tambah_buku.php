
<?php
include "koneksi.php";
if(isset($_POST['simpan'])) {
    $isbn = $_POST['isbn']; $judul = $_POST['judul']; $pengarang = $_POST['pengarang']; $stok = $_POST['stok'];
    mysqli_query($koneksi, "INSERT INTO buku (isbn, judul, pengarang, stok) VALUES ('$isbn', '$judul', '$pengarang', '$stok')");
    header("Location: kelola_buku.php");
}
?>