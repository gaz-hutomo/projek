<?php
// Mencegah error session_start() ganda
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php'; 

// Dapatkan nama file yang sedang diakses (misal: "login.php")
$current_page = basename($_SERVER['PHP_SELF']); 

// --- DAFTAR HALAMAN YANG TIDAK MEMERLUKAN SEARCH BAR ---
$excluded_pages = [
    // Halaman Katalog/Detail
    'detail_produk.php', 
    
    // Halaman Otentikasi
    'login.php', 
    'register.php',
    'logout.php',
    
    // Halaman Transaksi
    'keranjang.php', 
    'riwayat.php', 
    'checkout.php', 
    'struk_order.php',
    'confirm_delivery.php',
    'beri_ulasan.php',
    
    // Halaman Admin
    'admin_produk.php',
    'admin_order.php',
    'admin_kategori.php',
    'admin_banner.php',
    'edit_produk.php',
    'detail_order_admin.php'
];
// ---------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">TOKO KAMPUS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">

            <?php if (!in_array($current_page, $excluded_pages)): ?>
            <form class="d-flex me-auto ms-3" action="index.php" method="get">
                <input class="form-control me-2" type="search" name="keyword" placeholder="Cari barang..." aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Cari</button>
            </form>
            <?php endif; ?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Katalog</a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="keranjang.php">Keranjang (<?php echo isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0; ?>)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="riwayat.php">Riwayat</a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Halo, <?= $_SESSION['name'] ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <li><h6 class="dropdown-header text-danger">ADMIN PANEL</h6></li>
                                <li><a class="dropdown-item" href="admin_produk.php">Kelola Produk</a></li>
                                <li><a class="dropdown-item" href="admin_kategori.php">Kelola Kategori</a></li>
                                <li><a class="dropdown-item" href="admin_banner.php">Kelola Banner</a></li>
                                <li><a class="dropdown-item" href="admin_order.php">Kelola Pesanan</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Daftar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">