<?php include 'koneksi.php'; include 'header.php'; ?>
<?php
if (isset($_POST['daftar'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // NFR-01.1 Security
    
    $query = "INSERT INTO users (name, email, password_hash) VALUES ('$nama', '$email', '$pass')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Berhasil daftar! Silakan login.'); window.location='login.php';</script>";
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-lg">
            <div class="card-body">
                <h3 class="text-center">Daftar Akun</h3>
                <form method="post">
                    <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <button type="submit" name="daftar" class="btn btn-primary w-100">Daftar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>