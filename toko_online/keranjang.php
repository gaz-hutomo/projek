<?php include 'koneksi.php'; include 'header.php'; ?>
<h2>Keranjang Belanja</h2>
<table class="table table-bordered">
    <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
    <tbody>
        <?php
        $total = 0;
        if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
            foreach ($_SESSION['keranjang'] as $id_produk => $jumlah) {
                $ambil = mysqli_query($conn, "SELECT * FROM products WHERE id='$id_produk'");
                $produk = mysqli_fetch_assoc($ambil);
                $subtotal = $produk['price'] * $jumlah;
                $total += $subtotal;
// ... dalam loop foreach ($SESSION['keranjang'] as $id_produk => $jumlah)

                echo "<tr>
                    <td>{$produk['name']}</td>
                    <td>Rp ".number_format($produk['price'])."</td>
                    <td>$jumlah</td>
                    <td>Rp ".number_format($subtotal)."</td>
                    <td>
                        <a href='hapus_keranjang.php?id=$id_produk' class='btn btn-sm btn-danger'>Hapus</a>
                    </td>
                </tr>";
// ...
            }
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total Belanja</th>
            <th>Rp <?= number_format($total) ?></th>
        </tr>
    </tfoot>
</table>
<a href="index.php" class="btn btn-secondary">Lanjut Belanja</a>
<a href="checkout.php" class="btn btn-primary">Checkout</a>
<?php include 'footer.php'; ?>