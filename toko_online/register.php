<?php include 'koneksi.php'; include 'header.php'; ?>
<?php
// Inisialisasi variabel untuk pesan error/sukses
$error_message = '';

if (isset($_POST['daftar'])) {
    // Sanitize input
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_input = $_POST['password'];

    // 1. Cek apakah email sudah terdaftar
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $error_message = "Email ini sudah terdaftar. Silakan gunakan email lain atau login.";
    } else {
        // Enkripsi password
        $pass_hash = password_hash($password_input, PASSWORD_DEFAULT); 
        
        // 2. Insert data
        $query = "INSERT INTO users (name, email, password_hash, role) VALUES ('$nama', '$email', '$pass_hash', 'user')"; // Default role 'user'
        
        if (mysqli_query($conn, $query)) {
            // Redirect langsung ke login jika berhasil
            echo "<script>alert('Berhasil daftar! Silakan login.'); window.location='login.php';</script>";
            exit;
        } else {
            $error_message = "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}
?>

<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-12 col-sm-8 col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-dark text-white text-center rounded-top-4"> 
                <h3 class="mb-0 py-2"><i class="fas fa-user-plus"></i> Buat Akun Baru</h3>
            </div>
            <div class="card-body p-4">
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" class="form-control" required 
                                value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required 
                                value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="daftar" class="btn btn-success w-100 mt-2">Daftar Sekarang</button>
                    
                    <hr>
                    <p class="text-center small mb-0">Sudah punya akun? 
                        <a href="login.php" class="text-primary fw-bold">Login di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>