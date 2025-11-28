<?php
include 'koneksi.php';

// Data Admin yang ingin Anda buat
$email = "admin@toko.com";
$password_plain = "admin123"; // INI ADALAH PASSWORD UNTUK LOGIN

// Hashing password
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// Query untuk mencari atau membuat akun admin
$cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");

if(mysqli_num_rows($cek) > 0){
    // Jika email sudah ada, update password hash dan pastikan role-nya admin
    $query = "UPDATE users SET password_hash='$password_hash', role='admin' WHERE email='$email'";
    if(mysqli_query($conn, $query)){
        echo "<h1>Akun Admin Berhasil Diperbarui! ✅</h1>";
        echo "Email: <b>$email</b><br>";
        echo "Password Baru: <b>$password_plain</b><br>";
    } else {
        echo "Error saat update: " . mysqli_error($conn);
    }
} else {
    // Jika email belum ada, buat akun baru
    $query = "INSERT INTO users (name, email, password_hash, role) VALUES ('Administrator', '$email', '$password_hash', 'admin')";
    if(mysqli_query($conn, $query)){
        echo "<h1>Akun Admin Berhasil Dibuat! 🎉</h1>";
        echo "Email: <b>$email</b><br>";
        echo "Password: <b>$password_plain</b><br>";
    } else {
        echo "Error saat insert: " . mysqli_error($conn);
    }
}

echo "<br><a href='login.php'>Coba login sekarang</a>";
?>