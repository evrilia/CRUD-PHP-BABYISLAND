<?php
include 'conn.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM orders WHERE id_order = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='read.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location='read.php';</script>";
    }
    $stmt->close();
}
$conn->close();
