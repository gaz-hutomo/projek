<?php include 'koneksi.php'; include 'header.php'; 
// Cek akses admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit; }
?>

<h2 class="mb-4">Dashboard Admin: Kelola Pesanan</h2>

<table class="table table-hover table-bordered">
    <thead class="table-dark">
        <tr>
            <th>No Order</th>
            <th>Pelanggan</th>
            <th>Total</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $orders = mysqli_query($conn, "SELECT o.*, u.name as user_name 
                                        FROM orders o 
                                        JOIN users u ON o.user_id = u.id 
                                        ORDER BY o.created_at DESC");
        while ($o = mysqli_fetch_assoc($orders)) {
            $status_color = match($o['status']) {
                'pending' => 'warning',
                'paid' => 'info',
                'shipped' => 'primary',
                'completed' => 'success',
                default => 'secondary'
            };
            echo "<tr>
                <td>#{$o['id']}</td>
                <td>{$o['user_name']}</td>
                <td>Rp ".number_format($o['total_amount'])."</td>
                <td>".date('d/m/Y H:i', strtotime($o['created_at']))."</td>
                <td><span class='badge bg-{$status_color}'>".ucfirst($o['status'])."</span></td>
                <td>
                    <a href='detail_order_admin.php?id={$o['id']}' class='btn btn-sm btn-info text-white'>Lihat Detail & Status</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>