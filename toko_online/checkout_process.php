<?php
// 1. Pastikan sesi sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 2. HANYA include koneksi.php. Hapus include 'header.php'; dari sini!
include 'koneksi.php'; 

// Cek apakah request datang dari form POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: checkout.php");
    exit;
}

// Cek User dan Keranjang
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Anda harus login untuk melakukan checkout.'); window.location='login.php';</script>";
    exit;
}

if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang kosong.'); window.location='keranjang.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$keranjang_session = $_SESSION['keranjang'];

// Ambil data dari form checkout
$alamat_kirim = mysqli_real_escape_string($conn, $_POST['shipping_address']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

// Inisiasi Order
$total_amount = 0;
$order_items_data = [];
$order_status = 'pending'; 

// Mulai Transaksi Database
mysqli_begin_transaction($conn);

try {
    // 1. Ambil detail produk lengkap dari database (untuk harga dan cek stok)
    $product_ids = array_keys($keranjang_session);
    if (empty($product_ids)) {
         throw new Exception("Keranjang kosong.");
    }
    
    $id_list = implode(',', array_map('intval', $product_ids));
    $product_query = mysqli_query($conn, "SELECT id, name, price, stock FROM products WHERE id IN ($id_list)");
    
    $keranjang_final = [];
    
    // Loop untuk memproses setiap item dan cek stok
    while ($p = mysqli_fetch_assoc($product_query)) {
        $product_id = $p['id'];
        $quantity = $keranjang_session[$product_id];
        
        // Cek stok
        if ($p['stock'] < $quantity) {
            throw new Exception("Stok produk '{$p['name']}' tidak cukup. Tersisa {$p['stock']} buah.");
        }

        $total_amount += ($p['price'] * $quantity);
        
        // Siapkan data untuk dimasukkan ke order_items
        $order_items_data[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $p['price'] 
        ];

        // Simpan kuantitas untuk update stok
        $keranjang_final[$product_id] = $quantity;
    }

    if (empty($order_items_data)) {
        throw new Exception("Tidak ada produk valid yang ditemukan di keranjang.");
    }
    
    // 2. Insert ke Tabel Orders
    $order_number = 'ORD-' . time() . '-' . $user_id; 
    
    $order_query = "INSERT INTO orders (user_id, order_number, total_amount, status, shipping_address) 
                    VALUES ('$user_id', '$order_number', '$total_amount', '$order_status', '$alamat_kirim')";
    
    if (!mysqli_query($conn, $order_query)) {
        throw new Exception("Gagal membuat order: " . mysqli_error($conn));
    }
    $order_id = mysqli_insert_id($conn);
    
    // 3. Insert ke Tabel Order_Items
    $item_values = [];
    foreach ($order_items_data as $item) {
        $product_id = $item['product_id'];
        $qty = $item['quantity'];
        $price = $item['price'];
        $item_values[] = "('$order_id', '$product_id', '$qty', '$price')";
    }
    
    $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES " . implode(', ', $item_values);
    if (!mysqli_query($conn, $item_query)) {
        throw new Exception("Gagal memasukkan item order: " . mysqli_error($conn));
    }
    
    // 4. Insert ke Tabel Payments
    $payment_query = "INSERT INTO payments (order_id, method, status, amount) 
                      VALUES ('$order_id', '$payment_method', 'pending', '$total_amount')";
    if (!mysqli_query($conn, $payment_query)) {
        throw new Exception("Gagal merekam pembayaran: " . mysqli_error($conn));
    }

    // 5. Update Stok Produk (Kurangi stok)
    foreach ($keranjang_final as $product_id => $quantity) {
        $update_stock_query = "UPDATE products SET stock = stock - '$quantity' WHERE id = '$product_id'";
        if (!mysqli_query($conn, $update_stock_query)) {
            throw new Exception("Gagal mengurangi stok produk ID $product_id: " . mysqli_error($conn));
        }
    }
    
    // Commit Transaksi
    mysqli_commit($conn);
    
    // 6. Clear Keranjang dan Redirect ke Halaman Struk
    unset($_SESSION['keranjang']);
    echo "<script>alert('Checkout berhasil! Order #$order_number telah dibuat. Silakan selesaikan pembayaran.'); window.location='struk_order.php?id=$order_id';</script>";
    exit;

} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($conn);
    
    // Tampilkan pesan error spesifik dari Exception
    echo "<script>alert('Gagal memproses pesanan: " . addslashes($e->getMessage()) . "'); window.location='checkout.php';</script>";
    exit;
}
?>