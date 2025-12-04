<?php include 'koneksi.php'; include 'header.php'; 
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit; }

$id_produk = $_GET['id'];
$produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id='$id_produk'"));

if (isset($_POST['ubah'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    $foto_lama = $_POST['foto_lama'];
    $foto = $foto_lama;

    // Cek jika ada upload foto baru
    if ($_FILES['foto']['error'] === 0) {
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        // Pindahkan foto baru dan hapus foto lama
        move_uploaded_file($tmp, "assets/img/".$foto);
        if (file_exists("assets/img/".$foto_lama)) {
            unlink("assets/img/".$foto_lama);
        }
    }
    
    $query = "UPDATE products SET name='$nama', price='$harga', stock='$stok', description='$deskripsi', image='$foto' WHERE id='$id_produk'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk berhasil diubah!'); window.location='admin_produk.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah produk.');</script>";
    }
}
?>

<h2 class="mb-4">Edit Produk: <?= $produk['name'] ?></h2>
<div class="card p-4 shadow">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="foto_lama" value="<?= $produk['image'] ?>">
        
        <div class="mb-3"><label>Nama Produk</label><input type="text" name="nama" class="form-control" value="<?= $produk['name'] ?>" required></div>
        <div class="mb-3"><label>Harga</label><input type="number" name="harga" class="form-control" value="<?= $produk['price'] ?>" required></div>
        <div class="mb-3"><label>Stok</label><input type="number" name="stok" class="form-control" value="<?= $produk['stock'] ?>" required></div>
        <div class="mb-3"><label>Deskripsi</label><textarea name="deskripsi" class="form-control"><?= $produk['description'] ?></textarea></div>
        
        <div class="mb-3">
            <label>Foto Produk Saat Ini</label><br>
            <img src="assets/img/<?= $produk['image'] ?>" width="100" class="mb-2">
            <input type="file" name="foto" class="form-control">
            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
        </div>
        <div class="col-md-12 mb-2">
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <?php
                $cat_q = mysqli_query($conn, "SELECT * FROM categories");
                while($c = mysqli_fetch_assoc($cat_q)) {
                    echo "<option value='{$c['id']}'>{$c['name']}</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" name="ubah" class="btn btn-warning">Simpan Perubahan</button>
        <a href="admin_produk.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php include 'footer.php'; ?>