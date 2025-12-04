<?php 
include 'koneksi.php'; 
include 'header.php'; 

// Cek User dan Keranjang
if (!isset($_SESSION['user_id'])) { 
    echo "<script>alert('Anda harus login untuk melakukan checkout.'); window.location='login.php';</script>";
    exit;
}

if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang Anda kosong.'); window.location='index.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$keranjang_session = $_SESSION['keranjang'];
$total_amount = 0;
$keranjang_final = []; // Array baru untuk menyimpan data lengkap

// --- LOGIKA PERBAIKAN ERROR ARRAY OFFSET ---

// 1. Dapatkan ID produk yang ada di keranjang sesi
$product_ids = array_keys($keranjang_session);
if (empty($product_ids)) {
    echo "<script>alert('Keranjang Anda kosong.'); window.location='index.php';</script>";
    exit;
}

// 2. Buat string ID untuk query SQL (misal: '1, 5, 10')
$id_list = implode(',', array_map('intval', $product_ids));

// 3. Ambil detail produk dari database
$product_query = mysqli_query($conn, "SELECT id, name, price, stock FROM products WHERE id IN ($id_list)");

// 4. Gabungkan detail produk dengan kuantitas dari sesi
while ($p = mysqli_fetch_assoc($product_query)) {
    $product_id = $p['id'];
    
    // Cek jika produk ada di keranjang sesi
    if (isset($keranjang_session[$product_id])) {
        $quantity = $keranjang_session[$product_id];
        $keranjang_final[] = [
            'id' => $product_id,
            'name' => $p['name'],
            'price' => $p['price'],
            'quantity' => $quantity
        ];
    }
}

// Jika keranjang final kosong setelah penggabungan, mungkin ada produk yang sudah dihapus
if (empty($keranjang_final)) {
    // Kosongkan sesi keranjang dan berikan peringatan
    unset($_SESSION['keranjang']);
    echo "<script>alert('Produk di keranjang Anda tidak ditemukan di katalog.'); window.location='index.php';</script>";
    exit;
}
// --- AKHIR LOGIKA PERBAIKAN ---


// Ambil data user untuk isian default alamat
$user_q = mysqli_query($conn, "SELECT address FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user_q);
$default_address = $user_data['address'] ?? '';
?>

<div class="container mt-4">
    <h3 class="mb-4">Halaman Checkout</h3>
    
    <form action="checkout_process.php" method="post">
        
        <h4 class="mb-3">1. Rincian Pesanan</h4>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Produk</th>
                        <th class="text-center">Harga Satuan</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // GUNAKAN ARRAY FINAL YANG SUDAH LENGKAP DETAILNYA
                    foreach ($keranjang_final as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total_amount += $subtotal;
                    ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td class="text-center">Rp <?= number_format($item['price']) ?></td>
                        <td class="text-center"><?= $item['quantity'] ?></td>
                        <td class="text-end">Rp <?= number_format($subtotal) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">TOTAL KESELURUHAN</th>
                        <th class="text-end text-danger fs-5">Rp <?= number_format($total_amount) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <h4 class="mt-4 mb-3">2. Alamat Pengiriman</h4>
        <div class="mb-4">
            <label for="shipping_address" class="form-label">Alamat Lengkap (Termasuk Nama Penerima & No. Telp)</label>
            <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required placeholder="Contoh: Budi Santoso, 0812xxxx, Jl. Raya Kampus No. 10, RT 01 RW 02, Jakarta Selatan."><?= htmlspecialchars($default_address) ?></textarea>
            <small class="form-text text-muted">Pastikan alamat yang dimasukkan sudah benar dan lengkap.</small>
        </div>

        <h4 class="mt-4 mb-3">3. Pilih Metode Pembayaran</h4>
        <div class="card p-4 mb-4 shadow-sm bg-light">
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="bankTransfer" value="Bank Transfer" required>
                <label class="form-check-label fw-bold" for="bankTransfer">
                    <i class="fas fa-university text-primary me-2"></i> Transfer Bank (BCA/Mandiri)
                </label>
                <p class="text-muted small ms-4 mb-0">Pembayaran melalui transfer bank, verifikasi manual.</p>
            </div>
            <hr class="my-2">
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="eWallet" value="E-wallet" required>
                <label class="form-check-label fw-bold" for="eWallet">
                    <i class="fas fa-wallet text-success me-2"></i> E-wallet (Dana/Gopay/OVO)
                </label>
                <p class="text-muted small ms-4 mb-0">Instruksi pembayaran akan muncul di halaman struk.</p>
            </div>
            <hr class="my-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" required>
                <label class="form-check-label fw-bold" for="cod">
                    <i class="fas fa-truck text-warning me-2"></i> COD (Bayar di Tempat)
                </label>
                <p class="text-muted small ms-4 mb-0">Pembayaran tunai saat barang diterima (hanya berlaku di area tertentu).</p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 mb-5">
            <i class="fas fa-check-circle"></i> Proses Checkout
        </button>
    </form>

</div>

<?php include 'footer.php'; ?>