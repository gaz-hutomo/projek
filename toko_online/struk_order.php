<?php 
session_start();
include 'koneksi.php';

// Cek keamanan: Pastikan user login
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$id_order = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : die("<script>alert('ID Order tidak valid.'); window.location='riwayat.php';</script>");

// Ambil data order, pastikan order ID milik user yang sedang login
$order_query = mysqli_query($conn, "SELECT * FROM orders WHERE id='$id_order' AND user_id='$user_id'");
$order = mysqli_fetch_assoc($order_query);

if (!$order) {
    die("<script>alert('Pesanan tidak ditemukan atau bukan milik Anda.'); window.location='riwayat.php';</script>");
}

// Ambil item-item pesanan
$items_query = mysqli_query($conn, "SELECT oi.*, p.name as product_name 
                                     FROM order_items oi JOIN products p ON oi.product_id = p.id 
                                     WHERE oi.order_id='$id_order'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pesanan #<?= $order['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { font-size: 10pt; }
        }
    </style>
</head>
<body class="p-4">

<div class="container border p-5 shadow-sm bg-white" style="max-width: 800px;">
    <div class="row mb-4 border-bottom pb-2">
        <div class="col-6">
            <h4 class="text-primary">Struk Pesanan</h4>
            <p>ID Pesanan: <strong>#<?= $order['id'] ?></strong></p>
        </div>
        <div class="col-6 text-end">
            <p>Tanggal: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?><br>
               Status: <span class="badge bg-<?= ($order['status'] == 'completed' ? 'success' : 'warning') ?>"><?= ucfirst($order['status']) ?></span></p>
        </div>
    </div>
    
    <div class="mb-4">
        <h6>Informasi Pengiriman</h6>
        <p><?= $_SESSION['name'] ?> (<?= $_SESSION['email'] ?>)<br>
           Alamat: <?= nl2br($order['shipping_address']) ?></p>
    </div>

    <table class="table table-bordered table-sm">
        <thead class="bg-light">
            <tr><th>Produk</th><th>Harga Satuan</th><th>Qty</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            <?php while ($item = mysqli_fetch_assoc($items_query)): ?>
            <tr>
                <td><?= $item['product_name'] ?></td>
                <td>Rp <?= number_format($item['price']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>Rp <?= number_format($item['price'] * $item['quantity']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">TOTAL BAYAR</td>
                <td class="fw-bold text-danger">Rp <?= number_format($order['total_amount']) ?></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="mt-5 text-center">
        <p class="small text-muted">Terima kasih atas pesanan Anda.</p>
    </div>
    
    <div class="mt-4 text-center no-print">
        <button onclick="window.print()" class="btn btn-primary me-2">Cetak Struk</button>
        <a href="riwayat.php" class="btn btn-secondary">Kembali ke Riwayat</a>
    </div>
</div>

</body>
</html>