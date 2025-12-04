<?php
session_start();
include 'koneksi.php';

// Pastikan yang mengakses adalah User yang sedang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_order = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Cek keamanan: Pastikan order ID ini benar-benar milik user yang sedang login DAN statusnya 'shipped'
    $cek_order = mysqli_query($conn, "SELECT id, status FROM orders WHERE id='$id_order' AND user_id='$user_id'");
    $order = mysqli_fetch_assoc($cek_order);

    if ($order && $order['status'] == 'shipped') {
        // Update status menjadi 'completed' setelah dikonfirmasi pembeli
        mysqli_query($conn, "UPDATE orders SET status='completed' WHERE id='$id_order'");
        echo "<script>alert('Pesanan berhasil dikonfirmasi telah diterima. Terima kasih!'); window.location='riwayat.php';</script>";
    } else {
        echo "<script>alert('Aksi tidak valid atau pesanan belum dikirim.'); window.location='riwayat.php';</script>";
    }
} else {
    header("Location: riwayat.php");
}
?>