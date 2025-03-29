<?php
require_once("conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama = trim($_POST["nama"]);
    $phone = trim($_POST["phone"]);
    $id_produk = $_POST["id_produk"];
    $id_category = $_POST["id_category"];
    $address = trim($_POST["address"]);
    $time = $_POST["time"];
    $time_return = $_POST["time_return"];

    // Validasi tanggal sewa
    if ($time_return < $time) {
        echo "<script>alert('Tanggal kembali tidak boleh lebih awal dari tanggal sewa!'); window.history.back();</script>";
        exit();
    }

    // Ambil harga per hari dari produk yang dipilih
    $stmt = $conn->prepare("SELECT price FROM products WHERE id_produk = ?");
    $stmt->bind_param("i", $id_produk);
    $stmt->execute();
    $stmt->bind_result($harga_perhari);
    $stmt->fetch();
    $stmt->close();

    if (!$harga_perhari) {
        echo "<script>alert('Produk tidak ditemukan!'); window.history.back();</script>";
        exit();
    }

    // Hitung total harga berdasarkan jumlah hari sewa
    $date1 = new DateTime($time);
    $date2 = new DateTime($time_return);
    $interval = $date1->diff($date2);
    $jumlah_hari = $interval->days + 1; // Termasuk hari pertama
    $total_price = $jumlah_hari * $harga_perhari;

    // Simpan ke database menggunakan prepared statement (tanpa id_order karena auto-increment)
    $sql = "INSERT INTO orders (nama, phone, id_produk, id_category, address, time, time_return, total_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiissi", $nama, $phone, $id_produk, $id_category, $address, $time, $time_return, $total_price);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Pesanan berhasil disimpan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan pesanan!'); window.history.back();</script>";
    }

    // Tutup statement
    $stmt->close();
}

// Tutup koneksi
$conn->close();
?>
