<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">E-COMMERCE KAMPUS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="admin_produk.php">Kelola Produk</a></li>
                            <li class="nav-item"><a class="nav-link" href="admin_order.php">Pesanan Masuk</a></li>
                            <?php else: ?>
                                <?php 
                                    $count = 0;
                                    if(isset($_SESSION['keranjang'])) {
                                        $count = array_sum($_SESSION['keranjang']);
                                    }
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="keranjang.php">
                                        Keranjang 
                                        <?php if ($count > 0): ?>
                                            <span class="badge bg-danger rounded-pill"><?= $count ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
                            <?php endif; ?>
                            // ...
                        <li class="nav-item"><a class="nav-link btn btn-danger btn-sm text-white ms-2" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Daftar</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">