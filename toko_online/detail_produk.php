<?php include 'koneksi.php'; include 'header.php'; 

$id_produk = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id_produk'");
$produk = mysqli_fetch_assoc($query); 

if (!$produk) { echo "Produk tidak ditemukan."; exit; }

// --- LOGIKA HITUNG RATING ---
$review_query = mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as total_review FROM reviews WHERE product_id='$id_produk'");
$review_data = mysqli_fetch_assoc($review_query);
$avg_rating = round($review_data['avg_rating'], 1); // Bulatkan 1 desimal
$total_review = $review_data['total_review'];
// ----------------------------
?>

<div class="row">
    <div class="col-md-5">
        <img src="assets/img/<?= $produk['image'] ?>" class="img-fluid rounded shadow-sm">
    </div>
    <div class="col-md-7">
        <h1 class="display-5 fw-bold"><?= $produk['name'] ?></h1>
        
        <div class="mb-3">
            <span class="text-warning fs-4">
                <?php 
                // Logika Bintang Kuning
                for($i=1; $i<=5; $i++) {
                    if($i <= $avg_rating) echo "★";
                    else echo "☆"; 
                }
                ?>
            </span>
            <span class="ms-2 text-muted">(<?= $avg_rating ?>/5.0 dari <?= $total_review ?> ulasan)</span>
        </div>

        <h2 class="text-primary my-4">Rp <?= number_format($produk['price']) ?></h2>
        <p class="lead">Stok: <?= $produk['stock'] ?></p>
        <hr>
        <p><?= nl2br($produk['description']) ?></p>
        
        <?php if ($produk['stock'] > 0): ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="tambah_keranjang.php">
                    <input type="hidden" name="id" value="<?= $produk['id'] ?>">
                    <div class="d-flex align-items-center mb-3">
                        <label class="me-3 fw-bold">Jumlah:</label>
                        <input type="number" name="quantity" class="form-control me-3" value="1" min="1" max="<?= $produk['stock'] ?>" style="width: 80px;" required>
                        <button type="submit" class="btn btn-lg btn-success"><i class="fas fa-cart-plus"></i> Beli</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">Login untuk membeli.</div>
            <?php endif; ?>
        <?php else: ?>
             <div class="alert alert-danger">Stok Habis.</div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <h4 class="border-bottom pb-2">Ulasan Pembeli</h4>
        <?php
        $list_review = mysqli_query($conn, "SELECT r.*, u.name 
                                            FROM reviews r 
                                            JOIN users u ON r.user_id = u.id 
                                            WHERE r.product_id='$id_produk' 
                                            ORDER BY r.created_at DESC");
        
        if (mysqli_num_rows($list_review) > 0) {
            while ($r = mysqli_fetch_assoc($list_review)) {
                // Konversi angka rating ke bintang
                $stars = str_repeat("★", $r['rating']) . str_repeat("☆", 5 - $r['rating']);
                echo "
                <div class='card mb-3'>
                    <div class='card-body'>
                        <div class='d-flex justify-content-between'>
                            <h6 class='fw-bold'>{$r['name']}</h6>
                            <small class='text-muted'>".date('d M Y', strtotime($r['created_at']))."</small>
                        </div>
                        <div class='text-warning mb-2'>{$stars}</div>
                        <p class='mb-0'>{$r['comment']}</p>
                    </div>
                </div>";
            }
        } else {
            echo "<p class='text-muted'>Belum ada ulasan untuk produk ini.</p>";
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>