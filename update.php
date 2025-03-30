<?php
require_once("conn.php");

// Ambil data kategori untuk dropdown
$categories = [];
$result = $conn->query("SELECT * FROM categories");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Ambil data pesanan yang akan diupdate
$order = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM orders WHERE id_order = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        echo "<script>alert('Data pesanan tidak ditemukan!'); window.location.href='read.php';</script>";
        exit;
    }
    $stmt->close();
}

// Ambil data produk berdasarkan kategori
$products = [];
if ($order) {
    $query = "SELECT * FROM products WHERE id_category = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order['id_category']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Sewa</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Update Pesanan</h2>
        <?php if ($order): ?>
            <form method="POST" action="update_process.php">
                <input type="hidden" name="id" value="<?php echo $order['id_order']; ?>">
                
                <div class="form-group">
                    <label for="nama">Nama:</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $order['nama']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Nomor Telepon:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $order['phone']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="id_category">Pilih Kategori:</label>
                    <select id="id_category" name="id_category" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id_category']; ?>" <?php echo ($order['id_category'] == $category['id_category']) ? 'selected' : ''; ?>>
                                <?php echo $category['name_categories']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="id_produk">Pilih Produk:</label>
                    <select id="id_produk" name="id_produk" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id_produk']; ?>" data-price="<?php echo $product['price']; ?>" <?php echo ($order['id_produk'] == $product['id_produk']) ? 'selected' : ''; ?>>
                                <?php echo $product['name_produk']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="time">Dari Tanggal:</label>
                    <input type="date" id="time" name="time" value="<?php echo date('Y-m-d', strtotime($order['time'])); ?>" required>
                    
                    <label for="time_return">Sampai Tanggal:</label>
                    <input type="date" id="time_return" name="time_return" value="<?php echo date('Y-m-d', strtotime($order['time_return'])); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="total_price">Total Harga:</label>
                    <input type="text" id="total_price" name="total_price" value="<?php echo $order['total_price']; ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="address">Alamat Pengiriman:</label>
                    <textarea id="address" name="address" required><?php echo $order['address']; ?></textarea>
                </div>
                
                <div class="button-container">
                    <button type="submit">Update</button>
                    <button type="button" onclick="window.location.href='read.php'">Batal</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function () {
        // AJAX untuk update produk berdasarkan kategori
        $('#id_category').change(function () {
            var id_category = $(this).val();
            $.ajax({
                url: 'get_products.php',
                type: 'POST',
                data: {id_category: id_category},
                success: function (data) {
                    $('#id_produk').html(data);
                    $('#total_price').val('');
                }
            });
        });

        // Hitung total harga otomatis saat tanggal atau produk berubah
        $('#id_produk, #time, #time_return').change(function () {
            var price = $('#id_produk option:selected').data('price') || 0;
            var startDate = new Date($('#time').val());
            var endDate = new Date($('#time_return').val());
            
            if (!isNaN(startDate) && !isNaN(endDate) && price > 0) {
                var diffTime = Math.abs(endDate - startDate);
                var days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                var total = days * price;
                $('#total_price').val(total);
            } else {
                $('#total_price').val('');
            }
        });
    });
    </script>
</body>
</html>
