<?php include 'koneksi.php'; include 'header.php'; ?>
<?php
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($pass, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            
            if($row['role'] == 'admin') {
                echo "<script>window.location='admin_produk.php';</script>";
            } else {
                echo "<script>window.location='index.php';</script>";
            }
        } else { echo "<div class='alert alert-danger'>Password salah!</div>"; }
    } else { echo "<div class='alert alert-danger'>Email tidak ditemukan!</div>"; }
}
?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="text-center">Login</h3>
                <form method="post">
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <button type="submit" name="login" class="btn btn-success w-100">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>