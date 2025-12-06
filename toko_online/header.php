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
    'tambah_produk.php', 
    'edit_produk.php',
    'admin_order.php',
    'detail_order_admin.php',
    'admin_kategori.php',
    'admin_banner.php',
];
// ---------------------------------------------------

// Cek apakah halaman yang diakses adalah halaman Admin
$is_admin_page = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') && 
                 (
                    strpos($current_page, 'admin_') === 0 || 
                    $current_page == 'edit_produk.php' ||
                    $current_page == 'tambah_produk.php' ||
                    $current_page == 'detail_order_admin.php'
                 );
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        .admin-sidebar-wrapper {
            /* Menghitung tinggi agar menutupi sisa layar di bawah navbar */
            min-height: calc(100vh - 56px); /* 56px adalah tinggi standar navbar Bootstrap */
            padding-top: 1rem; 
        }
        .admin-sidebar .nav-link {
            color: #f8f9fa; 
            padding: 0.75rem 1rem; 
            transition: background-color 0.3s;
        }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
            background-color: #495057; 
            border-radius: 0.25rem;
        }
        /* Memberi padding atas pada konten admin agar tidak tertutup navbar */
        .admin-content {
            padding-top: 1rem !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">JAVA.STORE</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            
            <ul class="navbar-nav"> 
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Katalog</a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item ms-3"> 
                            <a class="btn btn-outline-info btn-sm" href="admin_produk.php"><i class="fas fa-tools"></i> Admin Panel</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user' && !$is_admin_page && !in_array($current_page, $excluded_pages)): ?>
                <li class="nav-item ms-3 d-flex">
                    <form class="d-flex" action="index.php" method="get">
                        <input class="form-control me-2" type="search" name="keyword" placeholder="Cari barang..." aria-label="Search">
                        <button class="btn btn-outline-light" type="submit">Cari</button>
                    </form>
                </li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'admin' && !in_array($current_page, $excluded_pages)): ?>
                <li class="nav-item ms-3 d-flex">
                    <form class="d-flex" action="index.php" method="get">
                        <input class="form-control me-2" type="search" name="keyword" placeholder="Cari barang..." aria-label="Search">
                        <button class="btn btn-outline-light" type="submit">Cari</button>
                    </form>
                </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
            
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang (<?php echo isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0; ?>)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="nav-link text-info" href="#"><i class="fas fa-user-circle"></i> Halo, <?= $_SESSION['name'] ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item ms-3">
                            <a class="nav-link text-info" href="admin_produk.php"><i class="fas fa-user-circle"></i> Halo, <?= $_SESSION['name'] ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    <?php endif; ?>
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

<?php if ($is_admin_page): ?>
<div class="row g-0"> 
    
    <div class="col-md-2 bg-dark admin-sidebar admin-sidebar-wrapper">
        <h6 class="text-white mb-4 border-bottom pb-2 pt-2 text-center">Admin Panel</h6> 
        <nav class="nav flex-column">
            <a class="nav-link <?= ($current_page == 'admin_produk.php' || $current_page == 'edit_produk.php' || $current_page == 'tambah_produk.php') ? 'active' : '' ?>" 
               href="admin_produk.php">
                <i class="fas fa-boxes fa-fw me-2"></i> Produk
            </a>
            <a class="nav-link <?= $current_page == 'admin_order.php' || $current_page == 'detail_order_admin.php' ? 'active' : '' ?>" 
               href="admin_order.php">
                <i class="fas fa-receipt fa-fw me-2"></i> Pesanan
            </a>
            <a class="nav-link <?= $current_page == 'admin_kategori.php' ? 'active' : '' ?>" 
               href="admin_kategori.php">
                <i class="fas fa-tags fa-fw me-2"></i> Kategori
            </a>
            <a class="nav-link <?= $current_page == 'admin_banner.php' ? 'active' : '' ?>" 
               href="admin_banner.php">
                <i class="fas fa-images fa-fw me-2"></i> Banner
            </a>
            <hr class="bg-secondary">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
            </a>
        </nav>
    </div>
    
    <div class="col-md-10 p-4 admin-content">
        <?php else: ?>
<div class="container mt-4">
<?php endif; ?>