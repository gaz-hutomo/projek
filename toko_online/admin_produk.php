<?php 
include 'koneksi.php'; 
include 'header.php'; 

// Cek akses admin: Pastikan pengguna adalah admin, jika tidak, arahkan ke login.
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); 
    exit; 
}

// ===============================================
// LOGIKA TAMBAH PRODUK
// ===============================================
if (isset($_POST['tambah'])) {
    // Ambil dan bersihkan input
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    
    // Pastikan category_id adalah integer
    $kategori = (int)$_POST['category_id']; 
    
    // Harga dan Stok divalidasi sebagai angka
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']); 
    
    // Proses Upload Gambar
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    
    // Tentukan direktori tujuan
    $upload_dir = "assets/img/";

    // Cek apakah direktori ada dan coba buat jika tidak ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Pindahkan file yang diunggah
    if (move_uploaded_file($tmp, $upload_dir . $foto)) {
        
        // PERBAIKAN UTAMA DI SINI:
        // Tambahkan kolom 'category_id' ke dalam query INSERT
        $query = "INSERT INTO products (name, price, stock, description, image, category_id) 
                  VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$foto', '$kategori')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Produk baru berhasil ditambahkan!'); window.location='admin_produk.php';</script>";
        } else {
            // Jika query gagal
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
                
                <div class="col-12 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php
                        $cat_q = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");
                        while($c = mysqli_fetch_assoc($cat_q)) {
                            echo "<option value='{$c['id']}'>{$c['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>

<h4 class="mb-3">Daftar Produk</h4>
<table class="table table-hover table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Foto</th>
            <th>Nama</th>
            <th>Kategori</th> <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Query untuk mengambil produk beserta nama kategori
        $products_query = "
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
        ";
        $products = mysqli_query($conn, $products_query);
        
        while ($p = mysqli_fetch_assoc($products)) {
            echo "<tr>
                <td><img src='assets/img/{$p['image']}' width='50' alt='{$p['name']}'></td>
                <td>{$p['name']}</td>
                <td>".(isset($p['category_name']) ? $p['category_name'] : 'N/A')."</td> 
                <td>Rp ".number_format($p['price'], 0, ',', '.')."</td>
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