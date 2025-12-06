<?php include 'koneksi.php'; include 'header.php'; ?>
<?php
// Pastikan session sudah dimulai di header.php

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password']; // Password tidak perlu di-escape sebelum verifikasi
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    // Inisialisasi variabel untuk menampung pesan error
    $error_message = '';

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($pass, $row['password_hash'])) {
            // Set session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['email'] = $row['email'];
            
            // Redirect
            if($row['role'] == 'admin') {
                echo "<script>window.location='admin_produk.php';</script>";
                exit;
            } else {
                echo "<script>window.location='index.php';</script>";
                exit;
            }
        } else { 
            $error_message = "Password salah!"; 
        }
    } else { 
        $error_message = "Email tidak ditemukan!"; 
    }
}
?>

<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-12 col-sm-8 col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-dark text-white text-center rounded-top-4">
                <h3 class="mb-0 py-2"><i class="fas fa-sign-in-alt"></i> Login</h3>
            </div>
            <div class="card-body p-4">
                
                <?php if (isset($error_message) && !empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-success w-100 mt-2">Masuk</button>
                    
                    <hr>
                    <p class="text-center small mb-0">Belum punya akun? 
                        <a href="register.php" class="text-primary fw-bold">Daftar di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>