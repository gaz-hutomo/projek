<?php include 'koneksi.php'; include 'header.php'; 

// Cek akses user terdaftar
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>

<div class="container mt-4">
    <h3 class="mb-4">Riwayat Belanja Anda</h3>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No Order</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $id = $_SESSION['user_id'];
                $query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$id' ORDER BY created_at DESC");
                
                if (mysqli_num_rows($query) == 0) {
                    echo "<tr><td colspan='5' class='text-center'>Belum ada riwayat pesanan.</td></tr>";
                }

                while ($o = mysqli_fetch_assoc($query)) {
                    
                    // --- 1. DEFINISI VARIABEL STATUS (Supaya tidak Undefined Variable) ---
                    // Kita set nilai default dulu
                    $status_display = ucfirst($o['status']);
                    $status_color = 'secondary';

                    // Kita ubah nilai sesuai status dari database
                    switch ($o['status']) {
                        case 'pending': 
                            $status_color = 'warning'; 
                            $status_display = 'Menunggu Pembayaran';
                            break;
                        case 'paid': 
                            $status_color = 'info'; 
                            $status_display = 'Dibayar (Menunggu Verifikasi)';
                            break;
                        case 'shipped': 
                            $status_color = 'primary';
                            $status_display = 'Dikirim (Menunggu Konfirmasi Anda)'; 
                            break;
                        case 'completed': 
                            $status_color = 'success';
                            $status_display = 'Selesai'; 
                            break;
                        case 'cancelled': 
                            $status_color = 'danger'; 
                            $status_display = 'Dibatalkan';
                            break;
                    }
                    // ---------------------------------------------------------------------

                    echo "<tr>
                        <td>#{$o['id']}</td>
                        <td>".date('d/m/Y H:i', strtotime($o['created_at']))."</td>
                        <td>Rp ".number_format($o['total_amount'])."</td>
                        <td><span class='badge bg-{$status_color}'>{$status_display}</span></td>
                        <td>";
                    
                    // --- 2. TOMBOL AKSI ---
                    
                    // A. Tombol Struk (Selalu Ada)
                    echo "<a href='struk_order.php?id={$o['id']}' class='btn btn-sm btn-info text-white me-1 mb-1'>Struk</a>";

                    // B. Tombol Konfirmasi (Hanya jika Shipped)
                    if ($o['status'] == 'shipped') {
                        echo "<a href='confirm_delivery.php?id={$o['id']}' class='btn btn-sm btn-success mb-1' onclick=\"return confirm('Apakah Anda yakin pesanan sudah diterima? Aksi ini akan menyelesaikan pesanan.');\">Konfirmasi Terima</a>";
                    } 
                    // C. Tombol Ulasan (Hanya jika Completed)
                    elseif ($o['status'] == 'completed') {
                        echo "<a href='beri_ulasan.php?id_order={$o['id']}' class='btn btn-sm btn-warning text-dark mb-1'>Beri Ulasan</a>";
                    }
                    
                    echo "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>