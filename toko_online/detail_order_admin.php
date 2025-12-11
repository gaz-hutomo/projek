<?php 
include 'koneksi.php'; 
include 'header.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); 
    exit; 
}

$id_order = mysqli_real_escape_string($conn, $_GET['id']);

// --- PERBAIKAN 1: AMBIL DATA ORDER TERLEBIH DAHULU ---
// Kita butuh data status saat ini SEBELUM memproses form update
$query_order = "SELECT o.*, u.name as user_name, u.email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id='$id_order'";
$order = mysqli_fetch_assoc(mysqli_query($conn, $query_order));

if (!$order) {
    echo "<div class='alert alert-danger'>Pesanan tidak ditemukan.</div>";
    include 'footer.php';
    exit;
}

// --- PERBAIKAN 2: PROSES UPDATE DENGAN VALIDASI ---
if (isset($_POST['update_status'])) {
    // Cek apakah status saat ini sudah 'completed'
    if ($order['status'] == 'completed') {
        echo "<script>
            alert('GAGAL: Pesanan ini sudah diselesaikan oleh pembeli. Status tidak dapat diubah lagi.'); 
            window.location='detail_order_admin.php?id=$id_order';
        </script>";
    } else {
        $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);
        
        // Update database
        mysqli_query($conn, "UPDATE orders SET status='$status_baru' WHERE id='$id_order'");
        
        // Refresh halaman agar data terbaru tampil
        echo "<script>
            alert('Status pesanan berhasil diubah menjadi ".ucfirst($status_baru)."'); 
            window.location='detail_order_admin.php?id=$id_order';
        </script>";
    }
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
            <h4>Status Pesanan</h4>
            
            <div class="mb-3">
                <label>Status Saat Ini:</label> <br>
                <?php
                $status_display = ucfirst($order['status']);
                $badge_class = 'bg-primary';
                
                if ($order['status'] == 'completed') {
                    $status_display = 'Selesai (Diterima Pembeli)';
                    $badge_class = 'bg-success';
                } else if ($order['status'] == 'shipped') {
                    $status_display = 'Dikirim';
                    $badge_class = 'bg-warning text-dark';
                } else if ($order['status'] == 'cancelled') {
                    $badge_class = 'bg-danger';
                }
                ?>
                <span class="badge <?= $badge_class ?> fs-5"><?= $status_display ?></span>
            </div>

            <hr>

            <?php if ($order['status'] == 'completed'): ?>
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>
                        <strong>Pesanan Selesai.</strong><br>
                        Pembeli telah mengkonfirmasi penerimaan barang. Status tidak dapat diubah lagi.
                    </div>
                </div>
            <?php else: ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="status_baru" class="form-label fw-bold">Ubah Status Menjadi:</label>
                        <select name="status_baru" class="form-select" required>
                            <option value="pending" <?= ($order['status'] == 'pending') ? 'selected' : '' ?>>Pending (Menunggu Pembayaran)</option>
                            <option value="paid" <?= ($order['status'] == 'paid') ? 'selected' : '' ?>>Paid (Pembayaran Diterima)</option>
                            <option value="shipped" <?= ($order['status'] == 'shipped') ? 'selected' : '' ?>>Dikirim (Item diserahkan ke kurir)</option>
                            <option value="cancelled" <?= ($order['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled (Dibatalkan)</option>
                        </select>
                        <div class="form-text text-muted">
                            Pilih status "Dikirim" agar pembeli dapat melakukan konfirmasi penerimaan.
                        </div>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">Update Status</button>
                </form>
            <?php endif; ?>
            </div>
    </div>
</div>

<h4>Item Pesanan:</h4>
<table class="table table-bordered table-striped">
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