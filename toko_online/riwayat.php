<?php include 'koneksi.php'; include 'header.php'; 

// Cek akses user terdaftar
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>

<h3>Riwayat Belanja Anda</h3>
<table class="table table-striped">
    <thead><tr><th>No Order</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php
        $id = $_SESSION['user_id'];
        $query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$id' ORDER BY created_at DESC");
        while ($o = mysqli_fetch_assoc($query)) {
            
            // --- Logika Penyesuaian Label Status ---
            $status_display = ucfirst($o['status']);
            $status_color = 'secondary';

            switch ($o['status']) {
                case 'pending': $status_color = 'warning'; break;
                case 'paid': $status_color = 'info'; break;
                case 'shipped': 
                    $status_color = 'primary';
                    $status_display = 'Dikirim (Menunggu Konfirmasi Anda)'; // Status yang memicu aksi
                    break;
                case 'completed': 
                    $status_color = 'success';
                    $status_display = 'Selesai (Dikonfirmasi)'; // Status akhir setelah konfirmasi
                    break;
                case 'cancelled': $status_color = 'danger'; break;
            }
            echo "<tr>
                <td>#{$o['id']}</td>
                <td>{$o['created_at']}</td>
                <td>Rp ".number_format($o['total_amount'])."</td>
                <td><span class='badge bg-{$status_color}'>{$status_display}</span></td>
                <td>";
            
            // --- Tombol Lihat Struk (Selalu Ada) ---
            echo "<a href='struk_order.php?id={$o['id']}' class='btn btn-sm btn-info text-white me-2'>Struk</a>";

            // --- Tombol Konfirmasi Penerimaan ---
            if ($o['status'] == 'shipped') {
                echo "<a href='confirm_delivery.php?id={$o['id']}' class='btn btn-sm btn-success' onclick=\"return confirm('Apakah Anda yakin pesanan sudah diterima? Aksi ini akan menyelesaikan pesanan.');\">Konfirmasi Penerimaan</a>";
            } else {
                // Jangan tampilkan apa-apa jika sudah confirmed atau status lain
            }
            
            echo "</td></tr>";
        }
        ?>
    </tbody>
</table>
<?php include 'footer.php'; ?>