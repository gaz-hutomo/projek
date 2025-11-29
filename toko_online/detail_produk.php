<?php include 'koneksi.php'; include 'header.php'; 

$id_produk = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id_produk'");
$produk = mysqli_fetch_assoc($query); 

if (!$produk) {
    echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
    include 'footer.php'; exit;
}
?>

<div class="row">
    <div class="col-md-5">
        <img src="assets/img/<?= $produk['image'] ?>" class="img-fluid rounded shadow-sm" alt="<?= $produk['name'] ?>">
    </div>
    <div class="col-md-7">
        <h1 class="display-5 fw-bold"><?= $produk['name'] ?></h1>
        <h2 class="text-primary my-4">Rp <?= number_format($produk['price']) ?></h2>
        
        <p class="lead">Stok Tersedia: <span class="badge bg-success"><?= $produk['stock'] ?></span></p>
        
        <hr>
        
        <h4>Deskripsi Produk:</h4>
        <p><?= nl2br($produk['description']) ?></p>
        
        <?php if ($produk['stock'] > 0): ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <form method="post" action="tambah_keranjang.php">
                    <input type="hidden" name="id" value="<?= $produk['id'] ?>">

                    <div class="d-flex align-items-center mb-3">
                        <label for="quantity" class="me-3 fw-bold">Jumlah Beli:</label>
                        <input type="number" 
                               name="quantity" 
                               id="quantity"
                               class="form-control me-3" 
                               value="1" 
                               min="1" 
                               max="<?= $produk['stock'] ?>" 
                               style="width: 80px;" 
                               required>

                        <button type="submit" class="btn btn-lg btn-success">
                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </form>

            <?php else: ?>
                <div class="alert alert-warning mt-3">Silakan <a href="login.php">Login</a> untuk menambahkan produk ini ke keranjang.</div>
            <?php endif; ?>
        <?php else: ?>
             <div class="alert alert-danger mt-3">Mohon maaf, stok produk ini telah habis.</div>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary mt-3 ms-2">Kembali ke Katalog</a>
    </div>
</div>

<?php include 'footer.php'; ?>