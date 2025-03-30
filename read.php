<?php
require_once("conn.php");
$sql = "SELECT o.id_order, o.nama, o.phone, p.name_produk, o.total_price FROM orders o JOIN products p ON o.id_produk = p.id_produk";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Pesanan</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="read-container">
        <h2>Daftar Pesanan</h2>
        <table class="read-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Phone</th>
                    <th>Produk</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row["id_order"]; ?></td>
                        <td><?php echo $row["nama"]; ?></td>
                        <td><?php echo $row["phone"]; ?></td>
                        <td><?php echo $row["name_produk"]; ?></td>
                        <td>Rp <?php echo number_format($row["total_price"], 0, ',', '.'); ?></td>
                        <td>
                            <a href="delete.php?id=<?php echo $row['id_order']; ?>" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $conn->close(); ?>