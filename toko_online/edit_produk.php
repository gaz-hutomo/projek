<?php 
include 'koneksi.php'; 
// Menggunakan header.php yang sudah diperbaiki untuk transisi
include 'header.php'; 

// Cek akses admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); 
    exit; 
}

$id_produk = mysqli_real_escape_string($conn, $_GET['id']);
$produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id='$id_produk'"));

// Pastikan produk ditemukan
if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='admin_produk.php';</script>";
    exit;
}

if (isset($_POST['ubah'])) {
    // 1. Tangkap variabel kategori baru (PERBAIKAN UTAMA 1)
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']); // <-- TANGKAP CATEGORY ID
    $foto_lama = mysqli_real_escape_string($conn, $_POST['foto_lama']);
    $foto = $foto_lama;

    // Cek jika ada upload foto baru
    if ($_FILES['foto']['error'] === 0 && !empty($_FILES['foto']['name'])) {
        $file_name = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        
        // Ganti nama file agar unik
        $foto = time() . "_" . $file_name;

        // Pindahkan foto baru dan hapus foto lama
        if (move_uploaded_file($tmp, "assets/img/".$foto)) {
            // Hapus foto lama hanya jika bukan default atau kosong
            if (!empty($foto_lama) && file_exists("assets/img/".$foto_lama)) {
                unlink("assets/img/".$foto_lama);
            }
        } else {
            // Jika gagal upload, gunakan foto lama
            $foto = $foto_lama;
        }
    }
    
    // 2. Kueri UPDATE dengan menyertakan category_id (PERBAIKAN UTAMA 2)
    $query = "UPDATE products SET 
                name='$nama', 
                price='$harga', 
                stock='$stok', 
                description='$deskripsi', 
                category_id='$category_id',  /* <-- KOLOM KATEGORI DISIMPAN */
                image='$foto' 
              WHERE id='$id_produk'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk berhasil diubah!'); window.location='admin_produk.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah produk: " . mysqli_error($conn) . "');</script>";
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
        
        <div class="col-md-12 mb-3">
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <?php
                $cat_q = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
                while($c = mysqli_fetch_assoc($cat_q)) {
                    // 3. Tampilkan kategori yang sedang dipilih (PERBAIKAN UTAMA 3)
                    $selected = ($c['id'] == $produk['category_id']) ? 'selected' : '';
                    echo "<option value='{$c['id']}' {$selected}>{$c['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Foto Produk Saat Ini</label><br>
            <img src="assets/img/<?= $produk['image'] ?>" width="100" class="mb-2">
            <input type="file" name="foto" class="form-control">
            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
        </div>
        
        <button type="submit" name="ubah" class="btn btn-warning">Simpan Perubahan</button>
        <a href="admin_produk.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php include 'footer.php'; ?>