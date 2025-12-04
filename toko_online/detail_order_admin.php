<?php include 'koneksi.php'; include 'header.php'; 
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit; }

$id_order = $_GET['id'];

// Ambil data order
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT o.*, u.name as user_name, u.email 
                                                 FROM orders o JOIN users u ON o.user_id = u.id 
                                                 WHERE o.id='$id_order'"));

// Proses Update Status
if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status_baru'];
    mysqli_query($conn, "UPDATE orders SET status='$status_baru' WHERE id='$id_order'");
    echo "<script>alert('Status pesanan berhasil diubah menjadi ".ucfirst($status_baru)."'); window.location='detail_order_admin.php?id=$id_order';</script>";
}
?>

<h2 class="mb-4">Detail Pesanan #<?= $order['id'] ?></h2>

<div class="row">
    <div class="col-md-6">
        <div class="card p-3 mb-4">
            <h4>Informasi Pelanggan & Pengiriman</h4>
            <p><strong>Nama:</strong> <?= $order['user_name'] ?><br>
               <strong>Email:</strong> <?= $order['email'] ?><br>
               <strong>Alamat Kirim:</strong> <?= nl2br($order['shipping_address']) ?></p>
            <p class="mt-3"><strong>Total Bayar:</strong> <span class="text-danger fw-bold fs-4">Rp <?= number_format($order['total_amount']) ?></span></p>
        </div>
    </div>
    <div class="col-md-6">
<div class="card p-3 mb-4">
    <h4>Ubah Status Pesanan</h4>
    <form method="post">
        <div class="mb-3">
            <label>Status Saat Ini</label>
            <?php
            // Penyesuaian Label untuk Admin View
            $current_status_label = ucfirst($order['status']);
            if ($order['status'] == 'completed') {
                $current_status_label = 'Selesai (Telah Dikonfirmasi Pembeli)';
            } else if ($order['status'] == 'shipped') {
                $current_status_label = 'Dikirim (Menunggu Konfirmasi Pembeli)';
            }
            ?>
            <span class="badge bg-primary fs-5"><?= $current_status_label ?></span>
        </div>
        <div class="mb-3">
            <label for="status_baru" class="form-label">Ubah Status Menjadi:</label>
            <select name="status_baru" class="form-select" required>
                <option value="pending" <?= ($order['status'] == 'pending') ? 'selected' : '' ?>>Pending (Menunggu Pembayaran)</option>
                <option value="paid" <?= ($order['status'] == 'paid') ? 'selected' : '' ?>>Paid (Pembayaran Diterima)</option>
                <option value="shipped" <?= ($order['status'] == 'shipped') ? 'selected' : '' ?>>Dikirim (Item telah diserahkan ke kurir)</option>
                
                <option value="cancelled" <?= ($order['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled (Dibatalkan)</option>
            </select>
            <small class="text-danger">Admin tidak dapat memilih status 'Selesai'. Status Selesai akan terisi otomatis setelah Pembeli mengkonfirmasi penerimaan.</small>
        </div>
        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
    </form>
</div>
    </div>
</div>

<h4>Item Pesanan:</h4>
<table class="table table-bordered">
    <thead class="table-dark">
        <tr><th>Produk</th><th>Harga Beli</th><th>Jumlah</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
        <?php
        // Ambil item pesanan
        $items = mysqli_query($conn, "SELECT oi.*, p.name as product_name 
                                      FROM order_items oi JOIN products p ON oi.product_id = p.id 
                                      WHERE oi.order_id='$id_order'");
        while ($item = mysqli_fetch_assoc($items)) {
            echo "<tr>
                <td>{$item['product_name']}</td>
                <td>Rp ".number_format($item['price'])."</td>
                <td>{$item['quantity']}</td>
                <td>Rp ".number_format($item['price'] * $item['quantity'])."</td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>