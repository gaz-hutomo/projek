<?php
include 'koneksi.php';

// 1. Setup data admin
$nama = "Administrator";
$email = "admin@toko.com";
$password_plain = "admin123"; // Password yang akan Anda ketik saat login
$role = "admin";

// 2. Hash password (Enkripsi)
// Penting: Kita biarkan PHP yang membuat hash agar cocok dengan verifikasi login
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// 3. Cek apakah email sudah ada biar tidak duplikat
$cek = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
if(mysqli_num_rows($cek) > 0){
    // Jika sudah ada, kita update saja password dan role-nya
    $query = "UPDATE users SET password_hash='$password_hash', role='$role', name='$nama' WHERE email='$email'";
    if(mysqli_query($conn, $query)){
        echo "<h1>Sukses!</h1>";
        echo "Akun admin lama diperbarui.<br>";
        echo "Email: <b>$email</b><br>";
        echo "Password: <b>$password_plain</b><br>";
        echo "<a href='login.php'>Klik disini untuk Login</a>";
    } else {
        echo "Error update: " . mysqli_error($conn);
    }
} else {
    // Jika belum ada, kita buat baru
    $query = "INSERT INTO users (name, email, password_hash, role) VALUES ('$nama', '$email', '$password_hash', '$role')";
    if(mysqli_query($conn, $query)){
        echo "<h1>Sukses!</h1>";
        echo "Akun admin baru berhasil dibuat.<br>";
        echo "Email: <b>$email</b><br>";
        echo "Password: <b>$password_plain</b><br>";
        echo "<a href='login.php'>Klik disini untuk Login</a>";
    } else {
        echo "Error insert: " . mysqli_error($conn);
    }
}
?>