<?php 
// Mencegah error session_start() ganda
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php'; 
include 'header.php'; 

// Cek akses admin yang ketat
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); exit; 
}

$upload_dir = "assets/banner/";

// Pastikan folder upload ada
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// --- LOGIKA TAMBAH BANNER ---
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

// --- LOGIKA EDIT BANNER BARU DITAMBAHKAN ---
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_banner']);
    $judul_baru = mysqli_real_escape_string($conn, $_POST['title_edit']);
    $link_baru = mysqli_real_escape_string($conn, $_POST['link_url_edit']);
    
    // Ambil data lama untuk referensi
    $ambil = mysqli_query($conn, "SELECT image_file FROM banners WHERE id='$id'");
    $data_lama = mysqli_fetch_assoc($ambil);
    $foto_lama = $data_lama['image_file'];

    // 1. Cek apakah ada file foto baru yang diupload
    if (!empty($_FILES['foto_edit']['name'])) {
        $foto_baru = $_FILES['foto_edit']['name'];
        $tmp_baru = $_FILES['foto_edit']['tmp_name'];
        
        // Buat nama file unik baru
        $ext = pathinfo($foto_baru, PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $ext;
        $file_target = $upload_dir . $new_filename;

        // Upload file baru
        if (move_uploaded_file($tmp_baru, $file_target)) {
            // Hapus file lama jika ada dan file baru berhasil diupload
            if ($foto_lama && file_exists($upload_dir . $foto_lama)) {
                unlink($upload_dir . $foto_lama);
            }
            // Gunakan nama file baru untuk query
            $query_update = "UPDATE banners SET image_file='$new_filename', title='$judul_baru', link_url='$link_baru' WHERE id='$id'";
        } else {
            echo "<script>alert('Gagal mengupload foto baru.'); window.location='admin_banner.php';</script>";
            exit;
        }

    } else {
        // 2. Tidak ada file baru yang diupload, hanya update judul dan link
        $query_update = "UPDATE banners SET title='$judul_baru', link_url='$link_baru' WHERE id='$id'";
    }

    // Eksekusi query update
    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Banner berhasil diupdate!'); window.location='admin_banner.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate banner: ".mysqli_error($conn)."'); window.location='admin_banner.php';</script>";
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
                    <button type='button' class='btn btn-warning btn-sm me-2' 
                            data-bs-toggle='modal' 
                            data-bs-target='#editModal' 
                            data-id='{$b['id']}' 
                            data-title='{$b['title']}' 
                            data-link='{$b['link_url']}' 
                            data-img='{$b['image_file']}'>
                        Edit
                    </button>
                    <a href='admin_banner.php?hapus={$b['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus banner?');\">Hapus</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_banner" id="id_banner">

                    <div class="mb-3">
                        <label for="title_edit" class="form-label">Judul Banner</label>
                        <input type="text" name="title_edit" id="title_edit" class="form-control" placeholder="Judul (Opsional)">
                    </div>

                    <div class="mb-3">
                        <label for="link_url_edit" class="form-label">Link Tujuan</label>
                        <input type="text" name="link_url_edit" id="link_url_edit" class="form-control" placeholder="Link Tujuan (e.g., detail_produk.php?id=1)">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gambar Saat Ini:</label>
                        <br><img id="current_image" src="" width="150" class="img-thumbnail">
                        <small class="d-block mt-1 text-muted">Abaikan jika tidak ingin mengganti gambar.</small>
                    </div>

                    <div class="mb-3">
                        <label for="foto_edit" class="form-label">Ganti Gambar Baru</label>
                        <input type="file" name="foto_edit" id="foto_edit" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="edit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            var button = event.relatedTarget; 
            
            // Ekstrak informasi dari atribut data-bs-*
            var id = button.getAttribute('data-id');
            var title = button.getAttribute('data-title');
            var link = button.getAttribute('data-link');
            var img_file = button.getAttribute('data-img');

            // Update konten modal
            var modalId = editModal.querySelector('#id_banner');
            var modalTitle = editModal.querySelector('#title_edit');
            var modalLink = editModal.querySelector('#link_url_edit');
            var modalImage = editModal.querySelector('#current_image');
            
            modalId.value = id;
            modalTitle.value = title;
            modalLink.value = link;
            // Pastikan path ke folder banner benar
            modalImage.src = '<?= $upload_dir ?>' + img_file; 
        });
    });
</script>

<?php include 'footer.php'; ?>
