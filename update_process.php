<?php
require_once("conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $phone = $_POST['phone'];
    $id_category = $_POST['id_category'];
    $id_produk = $_POST['id_produk'];
    $time = $_POST['time'];
    $time_return = $_POST['time_return'];
    $total_price = $_POST['total_price'];
    $address = $_POST['address'];

    $query = "UPDATE orders SET nama = ?, phone = ?, id_category = ?, id_produk = ?, time = ?, time_return = ?, total_price = ?, address = ? WHERE id_order = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiiisssi", $nama, $phone, $id_category, $id_produk, $time, $time_return, $total_price, $address, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='read.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Akses tidak diizinkan!'); window.location.href='read.php';</script>";
}