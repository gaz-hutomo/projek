<?php include 'koneksi.php'; include 'header.php'; 
// Cek akses admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header("Location: login.php"); exit; }

// Tambah Produk
if (isset($_POST['tambah'])) {
    // --- PENTING: GUNAKAN mysqli_real_escape_string UNTUK SEMUA INPUT STRING ---
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    // Harga dan Stok adalah angka, tidak perlu di-escape string, tapi perlu divalidasi
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']); // PERBAIKAN UTAMA DI SINI
    
    // Upload Gambar
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    
    // Cek apakah upload berhasil
    if (move_uploaded_file($tmp, "assets/img/".$foto)) {
        // Jika upload sukses, baru masukkan ke database
        $query = "INSERT INTO products (name, price, stock, description, image) 
                  VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$foto')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Produk baru berhasil ditambahkan!'); window.location='admin_produk.php';</script>";
        } else {
            // Jika query gagal (misalnya ada masalah lain)
            echo "<script>alert('Gagal menambahkan produk ke database: ".mysqli_error($conn)."');</script>";
        }
    } else {
        echo "<script>alert('Gagal mengupload foto. Pastikan folder assets/img ada dan memiliki izin tulis.');</script>";
    }
}
?>

<h2 class="mb-4">Dashboard Admin: Kelola Produk</h2>
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">Tambah Produk Baru</div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-2"><input type="text" name="nama" class="form-control" placeholder="Nama Produk" required></div>
                <div class="col-md-6 mb-2"><input type="number" name="harga" class="form-control" placeholder="Harga" required></div>
                <div class="col-md-6 mb-2"><input type="number" name="stok" class="form-control" placeholder="Stok" required></div>
                <div class="col-md-6 mb-2"><input type="file" name="foto" class="form-control" required></div>
                <div class="col-12 mb-2"><textarea name="deskripsi" class="form-control" placeholder="Deskripsi Produk"></textarea></div>
                <div class="col-12"><button type="submit" name="tambah" class="btn btn-primary">Simpan Produk</button></div>
            </div>
        </form>
    </div>
</div>

<table class="table table-hover table-bordered">
    <thead class="table-dark"><tr><th>Foto</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php
        $products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
        while ($p = mysqli_fetch_assoc($products)) {
            echo "<tr>
                <td><img src='assets/img/{$p['image']}' width='50'></td>
                <td>{$p['name']}</td>
                <td>Rp ".number_format($p['price'])."</td>
                <td>{$p['stock']}</td>
                <td>
                    <a href='edit_produk.php?id={$p['id']}' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='hapus_produk.php?id={$p['id']}' class='btn btn-danger btn-sm'>Hapus</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>
<?php include 'footer.php'; ?>