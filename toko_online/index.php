<?php include 'koneksi.php'; include 'header.php'; ?>

<div class="text-center mb-5">
    <h1 class="display-4 fw-bold">Selamat Datang</h1>
    <p class="lead">Belanja kebutuhan kuliah dan gadget terlengkap.</p>
</div>

<div class="row">
    <?php
    $query = mysqli_query($conn, "SELECT * FROM products");
    while ($p = mysqli_fetch_assoc($query)) :
    ?>
// ... dalam loop while ($p = mysqli_fetch_assoc($query))

            <div class="card-body">
                <h5 class="card-title"><?= $p['name'] ?></h5>
                <p class="card-text text-primary fw-bold">Rp <?= number_format($p['price']) ?></p>
                <p class="small text-muted"><?= substr($p['description'], 0, 50) ?>...</p>
                
                <a href="detail_produk.php?id=<?= $p['id'] ?>" class="btn btn-info btn-sm w-100 mb-2 text-white">
                    Lihat Detail
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="tambah_keranjang.php?id=<?= $p['id'] ?>" class="btn btn-success w-100">
                         + Keranjang
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-secondary w-100">Login untuk Beli</a>
                <?php endif; ?>
// ...
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>