<?php
include 'koneksi.php';
$id = $_GET['id'];
$ambil = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$pecah = mysqli_fetch_assoc($ambil);
$fotoproduk = $pecah['image'];

// Hapus file foto dari folder assets/img
if (file_exists("assets/img/$fotoproduk")) {
    unlink("assets/img/$fotoproduk");
}

// Hapus data dari database
mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
echo "<script>alert('Produk terhapus'); window.location='admin_produk.php';</script>";
?>