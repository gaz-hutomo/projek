<?php include 'koneksi.php'; include 'header.php'; 
// --- Ambil data banner aktif ---
// Ambil semua banner aktif, urutkan berdasarkan display_order atau ID terbaru
$banners_query = mysqli_query($conn, "SELECT * FROM banners WHERE is_active=1 ORDER BY display_order ASC, created_at DESC");
$banners_count = mysqli_num_rows($banners_query);
?>

<?php if ($banners_count > 0): ?>
<div id="bannerCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php for ($i = 0; $i < $banners_count; $i++): ?>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i == 0 ? 'active' : '' ?>" aria-current="<?= $i == 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
        <?php endfor; ?>
    </div>

    <div class="carousel-inner rounded-3 shadow">
        <?php 
        $i = 0;
        // Kita ulangi pengambilan data dari awal untuk menampilkan item
        mysqli_data_seek($banners_query, 0); 
        while ($b = mysqli_fetch_assoc($banners_query)): 
        ?>
        <div class="carousel-item <?= $i++ == 0 ? 'active' : '' ?>">
            <a href="<?= !empty($b['link_url']) ? htmlspecialchars($b['link_url']) : '#' ?>">
                <img src="assets/banner/<?= $b['image_file'] ?>" 
                     class="d-block w-100" 
                     alt="<?= $b['title'] ?>" 
                     style="height: 350px; object-fit: cover;">
            </a>
            <?php if (!empty($b['title'])): ?>
            <div class="carousel-caption d-none d-md-block text-start">
                <h5><?= $b['title'] ?></h5>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<?php else: ?>
<div class="p-5 mb-4 bg-light rounded-3 shadow-sm text-center">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold text-primary">Katalog Produk E-Commerce Kampus</h1>
        <p class="col-md-8 fs-4 mx-auto">Temukan semua kebutuhan perkuliahan dan gadget terbaru di sini.</p>
    </div>
</div>
<?php endif; ?>

<h3 class="mb-4 border-bottom pb-2">Produk Terbaru</h3>

<div class="row">
    <?php
    $query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC"); 
    
    if (mysqli_num_rows($query) == 0) {
        echo "<div class='col-12'><div class='alert alert-info'>Belum ada produk yang tersedia saat ini.</div></div>";
    }

    while ($p = mysqli_fetch_assoc($query)) :
    ?>
    
    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <a href="detail_produk.php?id=<?= $p['id'] ?>">
                <img src="assets/img/<?= $p['image'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Produk">
            </a>
            
            <div class="card-body d-flex flex-column">
                <h5 class="card-title text-truncate"><?= $p['name'] ?></h5>
                <p class="card-text text-primary fw-bold fs-4">Rp <?= number_format($p['price']) ?></p>
                <p class="small text-muted mb-auto">Stok: <?= $p['stock'] ?></p>
                
                <div class="mt-3">
                    <a href="detail_produk.php?id=<?= $p['id'] ?>" class="btn btn-primary w-100">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>