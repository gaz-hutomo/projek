<?php
session_start();
include 'koneksi.php'; 

// Cek apakah data dikirim melalui POST (dari detail_produk.php)
if (!isset($_POST['id']) || !isset($_POST['quantity'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_POST['id'];
$qty_tambah = (int)$_POST['quantity'];

// Validasi 1: Pastikan quantity minimal 1
if ($qty_tambah < 1) {
    echo "<script>alert('Jumlah beli harus minimal 1.'); window.location='detail_produk.php?id=$id';</script>";
    exit;
}

// Ambil data stok produk
$ambil = mysqli_query($conn, "SELECT stock, name FROM products WHERE id='$id'");
$produk = mysqli_fetch_assoc($ambil);

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan.'); window.location='index.php';</script>";
    exit;
}

// Inisialisasi keranjang jika kosong
if(!isset($_SESSION['keranjang'])) { $_SESSION['keranjang'] = []; }

// Hitung total kuantitas jika ditambahkan
$qty_sekarang = isset($_SESSION['keranjang'][$id]) ? $_SESSION['keranjang'][$id] : 0;
$qty_total_baru = $qty_sekarang + $qty_tambah;

// Validasi 2: Cek apakah total kuantitas melebihi stok yang ada
if ($qty_total_baru > $produk['stock']) {
    echo "<script>alert('Gagal! Total kuantitas item ({$produk['name']}) di keranjang ({$qty_total_baru}) melebihi stok yang tersedia ({$produk['stock']}).'); window.location='detail_produk.php?id=$id';</script>";
    exit;
}

// Tambahkan kuantitas ke keranjang
if(isset($_SESSION['keranjang'][$id])) { 
    $_SESSION['keranjang'][$id] = $qty_total_baru;
} else { 
    $_SESSION['keranjang'][$id] = $qty_tambah; 
}

header("Location: keranjang.php");
?>