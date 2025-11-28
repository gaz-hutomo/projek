<?php include 'koneksi.php'; include 'header.php'; ?>

<div class="p-5 mb-4 bg-light rounded-3 shadow-sm text-center">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold text-primary">Katalog Produk E-Commerce Kampus</h1>
        <p class="col-md-8 fs-4 mx-auto">Temukan semua kebutuhan perkuliahan dan gadget terbaru di sini. Siap diantar ke alamat Anda!</p>
    </div>
</div>

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