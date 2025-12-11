<?php include 'koneksi.php'; include 'header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$id_order = mysqli_real_escape_string($conn, $_GET['id_order']);
$user_id = $_SESSION['user_id'];

// Validasi: Pastikan order milik user & status completed
$cek_order = mysqli_query($conn, "SELECT * FROM orders WHERE id='$id_order' AND user_id='$user_id' AND status='completed'");
if (mysqli_num_rows($cek_order) == 0) {
    echo "<script>alert('Pesanan tidak valid atau belum selesai.'); window.location='riwayat.php';</script>";
    exit;
}

// Proses Simpan Ulasan
if (isset($_POST['kirim_ulasan'])) {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $komentar = mysqli_real_escape_string($conn, $_POST['komentar']);
    
    // Cek duplikasi ulasan
    $cek_review = mysqli_query($conn, "SELECT id FROM reviews WHERE order_id='$id_order' AND product_id='$product_id'");
    if (mysqli_num_rows($cek_review) == 0) {
        mysqli_query($conn, "INSERT INTO reviews (order_id, product_id, user_id, rating, comment) VALUES ('$id_order', '$product_id', '$user_id', '$rating', '$komentar')");
        echo "<script>alert('Ulasan berhasil dikirim!'); window.location='beri_ulasan.php?id_order=$id_order';</script>";
    }
}
?>

<h3 class="mb-4">Beri Ulasan Produk (Order #<?= $id_order ?>)</h3>
<a href="riwayat.php" class="btn btn-secondary mb-3">&laquo; Kembali</a>

<div class="row">
    <?php
    // Ambil item produk dalam order ini
    $items = mysqli_query($conn, "SELECT oi.*, p.name, p.image 
                                  FROM order_items oi 
                                  JOIN products p ON oi.product_id = p.id 
                                  WHERE oi.order_id='$id_order'");
    
    while ($item = mysqli_fetch_assoc($items)):
        // Cek apakah produk ini sudah diulas di order ini
        $cek_review = mysqli_query($conn, "SELECT * FROM reviews WHERE order_id='$id_order' AND product_id='{$item['product_id']}'");
        $sudah_ulas = mysqli_fetch_assoc($cek_review);
    ?>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body d-flex">
                <img src="assets/img/<?= $item['image'] ?>" style="width: 80px; height: 80px; object-fit: cover;" class="rounded me-3">
                <div class="w-100">
                    <h5 class="card-title"><?= $item['name'] ?></h5>
                    
                    <?php if ($sudah_ulas): ?>
                        <div class="alert alert-success py-2 mb-0">
                            <strong>Nilai: <?= $sudah_ulas['rating'] ?>/5</strong><br>
                            <small>"<?= $sudah_ulas['comment'] ?>"</small>
                        </div>
                    <?php else: ?>
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <div class="mb-2">
                                <label>Rating:</label>
                                <select name="rating" class="form-select form-select-sm d-inline-block w-auto" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                    <option value="4">⭐⭐⭐⭐ (4)</option>
                                    <option value="3">⭐⭐⭐ (3)</option>
                                    <option value="2">⭐⭐ (2)</option>
                                    <option value="1">⭐ (1)</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <textarea name="komentar" class="form-control form-control-sm" placeholder="Tulis ulasan Anda..." required></textarea>
                            </div>
                            <button type="submit" name="kirim_ulasan" class="btn btn-primary btn-sm">Kirim Ulasan</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>