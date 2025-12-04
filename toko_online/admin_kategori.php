<?php include 'koneksi.php'; include 'header.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit; }

// --- TAMBAH KATEGORI ---
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$nama')");
    echo "<script>alert('Kategori berhasil ditambahkan'); window.location='admin_kategori.php';</script>";
}

// --- HAPUS KATEGORI ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");
    // Reset produk yang menggunakan kategori ini menjadi NULL (agar tidak error)
    mysqli_query($conn, "UPDATE products SET category_id=NULL WHERE category_id='$id'");
    echo "<script>alert('Kategori dihapus'); window.location='admin_kategori.php';</script>";
}
?>

<div class="container mt-4">
    <h3 class="mb-4">Kelola Kategori Produk</h3>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Tambah Kategori</div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label>Nama Kategori Baru</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Tablet">
                        </div>
                        <button type="submit" name="tambah" class="btn btn-success w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark"><tr><th>No</th><th>Nama Kategori</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
                            $no = 1;
                            while($c = mysqli_fetch_assoc($query)):
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $c['name'] ?></td>
                                <td>
                                    <a href="admin_kategori.php?hapus=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>