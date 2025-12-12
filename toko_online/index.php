<?php 
include 'koneksi.php'; 
// Pastikan file header.php Anda adalah versi terakhir yang mengandung CSS dan JS transisi
include 'header.php'; 

// --- Ambil data banner aktif untuk Carousel ---
// Catatan: Asumsi kolom 'is_active' dan 'display_order' ada di tabel 'banners'.
$banners_query = mysqli_query($conn, "SELECT * FROM banners WHERE is_active=1 ORDER BY display_order ASC, created_at DESC");
$banners_count = mysqli_num_rows($banners_query);

// --- Ambil data kategori untuk filter ---
$cats_query = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>

<?php if ($banners_count > 0): ?>
<div id="bannerCarousel" class="carousel slide mb-5" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-indicators">
        <?php for ($i = 0; $i < $banners_count; $i++): ?>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i == 0 ? 'active' : '' ?>" aria-current="<?= $i == 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
        <?php endfor; ?>
    </div>

    <div class="carousel-inner rounded-3 shadow">
        <?php 
        $i = 0;
        mysqli_data_seek($banners_query, 0); 
        while ($b = mysqli_fetch_assoc($banners_query)): 
        ?>
        <div class="carousel-item <?= $i++ == 0 ? 'active' : '' ?>">
            <a href="<?= !empty($b['link_url']) ? htmlspecialchars($b['link_url']) : '#' ?>">
                <img src="assets/banner/<?= $b['image_file'] ?>" 
                     class="d-block w-100 carousel-img-fit" 
                     alt="<?= $b['title'] ?>">
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

<h3 class="mb-3 border-bottom pb-2">Katalog Produk</h3>

<div class="mb-4 d-flex flex-wrap">
    <a href="index.php" class="btn btn-outline-dark me-2 mb-2 <?= (!isset($_GET['kategori']) && !isset($_GET['keyword'])) ? 'active' : '' ?>">Semua</a>
    
    <?php
    // Reset pointer kueri kategori
    mysqli_data_seek($cats_query, 0);
    while($c = mysqli_fetch_assoc($cats_query)):
        $active = (isset($_GET['kategori']) && $_GET['kategori'] == $c['id']) ? 'active' : '';
    ?>
    <a href="index.php?kategori=<?= $c['id'] ?>" class="btn btn-outline-dark me-2 mb-2 <?= $active ?>">
        <?= $c['name'] ?>
    </a>
    <?php endwhile; ?>
</div>

<?php 
    if(isset($_GET['keyword'])) {
        echo "<p class='text-muted'>Menampilkan hasil pencarian untuk: <strong>\"".htmlspecialchars($_GET['keyword'])."\"</strong></p>";
    } elseif(isset($_GET['kategori'])) {
        $cat_id = mysqli_real_escape_string($conn, $_GET['kategori']);
        $cat_name_q = mysqli_query($conn, "SELECT name FROM categories WHERE id='$cat_id'");
        $cat_name = mysqli_fetch_assoc($cat_name_q)['name'] ?? 'Kategori Tidak Dikenal';
        echo "<p class='text-muted'>Menampilkan produk dalam kategori: <strong>{$cat_name}</strong></p>";
    }
?>

<div class="row">
    <?php
    // --- LOGIKA QUERY PENCARIAN & FILTER ---
    $where = "1=1"; 

    // 1. Jika ada pencarian Keyword
    if (isset($_GET['keyword'])) {
        $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
        $where .= " AND name LIKE '%$keyword%'";
    }

    // 2. Jika ada filter Kategori
    if (isset($_GET['kategori'])) {
        $cat_id = mysqli_real_escape_string($conn, $_GET['kategori']);
        // PASTIKAN NAMA KOLOM ADALAH category_id (sesuai database Anda)
        $where .= " AND category_id = '$cat_id'"; 
    }

    $query = mysqli_query($conn, "SELECT * FROM products WHERE $where ORDER BY id DESC"); 
    
    // Cek hasil
    if (mysqli_num_rows($query) == 0) {
        echo "<div class='col-12 text-center py-5'>
                <div class='alert alert-warning'>Produk tidak ditemukan. Coba kata kunci lain atau kategori berbeda.</div>
                <a href='index.php' class='btn btn-dark mt-3'>Lihat Semua Produk</a>
              </div>";
    }

    while ($p = mysqli_fetch_assoc($query)) :
    ?>
    
    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <a href="detail_produk.php?id=<?= $p['id'] ?>">
                <img src="assets/img/<?= $p['image'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= $p['name'] ?>">
            </a>
            
            <div class="card-body d-flex flex-column">
                <h5 class="card-title text-truncate"><?= $p['name'] ?></h5>
                <p class="card-text text-black fw-bold fs-4">Rp <?= number_format($p['price']) ?></p>
                
                <p class="small text-muted mb-auto">Stok: <?= $p['stock'] ?></p>
                
                <div class="mt-3">
                    <a href="detail_produk.php?id=<?= $p['id'] ?>" class="btn btn-success w-100">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>
