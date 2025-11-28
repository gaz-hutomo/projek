<?php
session_start();
$id = $_GET['id'];
// Jika keranjang kosong, inisialisasi array
if(!isset($_SESSION['keranjang'])) { $_SESSION['keranjang'] = []; }
// Tambah jumlah jika sudah ada, atau set 1 jika belum
if(isset($_SESSION['keranjang'][$id])) { $_SESSION['keranjang'][$id] += 1; }
else { $_SESSION['keranjang'][$id] = 1; }
header("Location: keranjang.php");
?>