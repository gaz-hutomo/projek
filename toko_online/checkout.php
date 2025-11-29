<?php include 'koneksi.php'; include 'header.php'; 
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); }

if (isset($_POST['checkout'])) {
    $user_id = $_SESSION['user_id'];
    $alamat = $_POST['alamat'];
    $total_belanja = 0;

    // 1. Hitung Total Lagi (Keamanan)
    foreach ($_SESSION['keranjang'] as $id => $qty) {
        $q = mysqli_query($conn, "SELECT price FROM products WHERE id='$id'");
        $d = mysqli_fetch_assoc($q);
        $total_belanja += ($d['price'] * $qty);
    }

    // 2. Simpan ke tabel Orders
    $query_order = "INSERT INTO orders (user_id, total_amount, status, shipping_address) 
                    VALUES ('$user_id', '$total_belanja', 'pending', '$alamat')";
    mysqli_query($conn, $query_order);
    $order_id = mysqli_insert_id($conn);

    // 3. Simpan ke tabel Order_Items dan Kurangi Stok (FR-04.3)
    foreach ($_SESSION['keranjang'] as $id => $qty) {
        $q = mysqli_query($conn, "SELECT price FROM products WHERE id='$id'");
        $d = mysqli_fetch_assoc($q);
        $harga = $d['price'];
        
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) 
                             VALUES ('$order_id', '$id', '$qty', '$harga')");
        
        // Kurangi stok
        mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id='$id'");
    }

    // 4. Kosongkan keranjang
    unset($_SESSION['keranjang']);
    echo "<script>alert('Pesanan berhasil! Silakan lakukan pembayaran manual.'); window.location='riwayat.php';</script>";
}
?>

<div class="row">
    <div class="col-md-6">
        <h3>Konfirmasi Pengiriman</h3>
        <form method="post">
            <div class="mb-3">
                <label>Alamat Lengkap Pengiriman</label>
                <textarea name="alamat" class="form-control" rows="3" required></textarea>
            </div>
            <div class="alert alert-info">
                <strong>Metode Pembayaran:</strong><br>
                Transfer Manual ke BCA: 123-456-789 (a.n Toko Kampus)<br>
                Silakan upload bukti bayar nanti di menu riwayat.
            </div>
            <button type="submit" name="checkout" class="btn btn-primary">Buat Pesanan</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>