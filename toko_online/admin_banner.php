<?php include 'koneksi.php'; include 'header.php'; 

// Cek akses admin yang ketat
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); exit; 
}

$upload_dir = "assets/banner/";

// Pastikan folder upload ada
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// --- LOGIKA TAMBAH BANNER (Fungsi yang Anda cari) ---
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['title']);
    $link = mysqli_real_escape_string($conn, $_POST['link_url']);
    
    // Cek apakah ada file yang diupload
    if (empty($_FILES['foto']['name'])) {
        echo "<script>alert('Anda harus memilih file gambar untuk banner.'); window.location='admin_banner.php';</script>";
        exit;
    }
    
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    
    // Pastikan nama file unik untuk mencegah konflik
    $ext = pathinfo($foto, PATHINFO_EXTENSION);
    $new_filename = time() . '_' . uniqid() . '.' . $ext;
    
    $file_target = $upload_dir . $new_filename;

    if (move_uploaded_file($tmp, $file_target)) {
        $query = "INSERT INTO banners (image_file, title, link_url) VALUES ('$new_filename', '$judul', '$link')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Banner berhasil ditambahkan!'); window.location='admin_banner.php';</script>";
        } else {
            // Jika gagal simpan ke DB, hapus file yang sudah diupload
            unlink($file_target);
            echo "<script>alert('Gagal menambahkan banner ke database: ".mysqli_error($conn)."');</script>";
        }
    } else {
        echo "<script>alert('Gagal mengupload foto. Pastikan folder assets/banner ada dan memiliki izin tulis.');</script>";
    }
}

// --- LOGIKA HAPUS BANNER ---
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $ambil = mysqli_query($conn, "SELECT image_file FROM banners WHERE id='$id'");
    $data = mysqli_fetch_assoc($ambil);
    
    if ($data) {
        // Hapus file fisik
        if (file_exists($upload_dir . $data['image_file'])) {
            unlink($upload_dir . $data['image_file']);
        }
        // Hapus dari database
        mysqli_query($conn, "DELETE FROM banners WHERE id='$id'");
        echo "<script>alert('Banner berhasil dihapus!'); window.location='admin_banner.php';</script>";
    } else {
        echo "<script>alert('Banner tidak ditemukan.'); window.location='admin_banner.php';</script>";
    }
}
?>

<h2 class="mb-4">Dashboard Admin: Kelola Banner Iklan</h2>
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">Tambah Banner Baru</div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4 mb-2"><input type="text" name="title" class="form-control" placeholder="Judul (Opsional)"></div>
                <div class="col-md-4 mb-2"><input type="text" name="link_url" class="form-control" placeholder="Link Tujuan (e.g., detail_produk.php?id=1)"></div>
                <div class="col-md-4 mb-2"><input type="file" name="foto" class="form-control" required></div>
                <div class="col-12"><button type="submit" name="tambah" class="btn btn-primary">Simpan Banner</button></div>
            </div>
        </form>
    </div>
</div>

<table class="table table-hover table-bordered">
    <thead class="table-dark"><tr><th>#</th><th>Preview</th><th>Judul</th><th>Link</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php
        $banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
        $no = 1;
        while ($b = mysqli_fetch_assoc($banners)) {
            $current_no = $no++; 
            
            echo "<tr>
                <td>{$current_no}</td>
                <td><img src='assets/banner/{$b['image_file']}' width='150' style='height: 50px; object-fit: cover;'></td>
                <td>{$b['title']}</td>
                <td><small>{$b['link_url']}</small></td>
                <td>
                    <a href='admin_banner.php?hapus={$b['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus banner?');\">Hapus</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>
<?php include 'footer.php'; ?>