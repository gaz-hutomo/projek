<?php include 'koneksi.php'; include 'header.php'; ?>
<h3>Riwayat Belanja Anda</h3>
<table class="table table-striped">
    <thead><tr><th>No Order</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
        <?php
        $id = $_SESSION['user_id'];
        $query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$id' ORDER BY created_at DESC");
        while ($o = mysqli_fetch_assoc($query)) {
            echo "<tr>
                <td>#{$o['id']}</td>
                <td>{$o['created_at']}</td>
                <td>Rp ".number_format($o['total_amount'])."</td>
                <td><span class='badge bg-warning text-dark'>{$o['status']}</span></td>
            </tr>";
        }
        ?>
    </tbody>
</table>
<?php include 'footer.php'; ?>