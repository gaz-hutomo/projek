<?php
// Pastikan semua script admin diawali dengan koneksi dan cek sesi
session_start();
include 'koneksi.php'; 

// --- PENTING: Cek Perizinan Admin (Keamanan) ---
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    echo "<script>alert('Akses Ditolak. Silakan login sebagai Admin.'); window.location='login.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']); // Amankan input

// 1. Ambil nama file foto sebelum menghapus data produk
$ambil = mysqli_query($conn, "SELECT image FROM products WHERE id='$id'");
$pecah = mysqli_fetch_assoc($ambil);

if ($pecah) {
    $fotoproduk = $pecah['image'];
    
    // --- SOLUSI FOREIGN KEY ERROR DIMULAI DI SINI ---
    
    // 2. Hapus semua entri di tabel 'reviews' yang merujuk ke produk ini.
    // Ini adalah CHILD table yang menyebabkan error pada pesan Anda.
    mysqli_query($conn, "DELETE FROM reviews WHERE product_id='$id'");
    
    // 3. Hapus semua entri di tabel 'order_items' yang merujuk ke produk ini.
    // Ini adalah CHILD table lain yang mungkin memiliki keterkaitan.
    mysqli_query($conn, "DELETE FROM order_items WHERE product_id='$id'");
    
    // 4. Hapus file foto dari folder assets/img
    if (!empty($fotoproduk) && file_exists("assets/img/$fotoproduk")) {
        unlink("assets/img/$fotoproduk");
    }

    // 5. Hapus data produk dari tabel 'products' (parent table).
    // Sekarang penghapusan ini akan diizinkan.
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    
    // --- SOLUSI FOREIGN KEY ERROR SELESAI ---

    echo "<script>alert('Produk dan semua data terkait (ulasan, riwayat pembelian) berhasil dihapus!'); window.location='admin_produk.php';</script>";

} else {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='admin_produk.php';</script>";
}
?>