<?php
require_once("conn.php");

$categories = [];
$result = $conn->query("SELECT * FROM categories");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

if (isset($_POST['id_category'])) {
    $id_category = $_POST['id_category'];
    $query = "SELECT * FROM products WHERE id_category = '$id_category'";
    $result = $conn->query($query);

    $output = "<option value=''>-- Pilih Peralatan --</option>";
    while ($row = $result->fetch_assoc()) {
        $output .= "<option value='{$row['id_produk']}'>{$row['name_produk']}</option>";
    }
    echo $output;
    exit;
}

// Jika request AJAX untuk mendapatkan harga produk
if (isset($_POST['id_produk'])) {
    $id_produk = $_POST['id_produk'];
    $query = "SELECT price FROM products WHERE id_produk = '$id_produk'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    echo $row['price']; // Kirim harga ke AJAX
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Sewa Perlengkapan Bayi</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Form Sewa Perlengkapan Bayi</h2>
        <form method="POST" action="create.php">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" required>
            </div>

            <div class="form-group">
                <label for="phone">phone Telepon:</label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <!-- Pilih category -->
            <div class="form-group">
                <label for="id_category">Pilih category:</label>
                <select id="id_category" name="id_category" required>
                    <option value="">-- Pilih category --</option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['id_category']; ?>">
                            <?php echo $category['name_categories']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <!-- Pilih Produk -->
            <div class="form-group">
                <label for="id_produk">Pilih Peralatan:</label>
                <select id="id_produk" name="id_produk" required>
                    <option value="">-- Pilih Peralatan --</option>
                </select>
            </div>

            <!-- Menampilkan Harga per Hari -->
            <div class="form-group">
                <label for="harga_per_hari">Harga per Hari:</label>
                <input type="text" id="harga_per_hari" name="harga_per_hari" readonly>
            </div>

            <!-- Pilih Tanggal Sewa -->
            <div class="form-group">
                <label for="time">Dari Tanggal:</label>
                <input type="date" id="time" name="time" required>

                <label for="time_return">Sampai Tanggal:</label>
                <input type="date" id="time_return" name="time_return" required>
            </div>

            <!-- Total Harga -->
            <div class="form-group">
                <label for="total_price">Total Harga:</label>
                <input type="text" id="total_price" name="total_price" readonly>
            </div>

            <div class="form-group">
                <label for="address">address Pengiriman:</label>
                <textarea id="address" name="address" required></textarea>
            </div>

            <div class="button-container">
                <button type="submit">Sewa</button>
                <button type="button" onclick="window.location.href='read.php'">Tampilkan Data</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Mengambil produk berdasarkan category yang dipilih
            $("#id_category").change(function() {
                var id_category = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "index.php",
                    data: {
                        id_category: id_category
                    },
                    success: function(response) {
                        $("#id_produk").html(response);
                        $("#harga_per_hari").val(""); // Reset harga
                        $("#total_price").val(""); // Reset total harga
                    }
                });
            });

            // Mengambil harga per hari dari database berdasarkan produk yang dipilih
            $("#id_produk").change(function() {
                var id_produk = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "index.php",
                    data: {
                        id_produk: id_produk
                    },
                    success: function(response) {
                        $("#harga_per_hari").val(response);
                        hitungTotalHarga();
                    }
                });
            });

            // Hitung total harga berdasarkan tanggal
            $("#time, #time_return").change(function() {
                hitungTotalHarga();
            });

            function hitungTotalHarga() {
                var hargaPerHari = parseFloat($("#harga_per_hari").val()) || 0;
                var tglMulai = new Date($("#time").val());
                var tglSelesai = new Date($("#time_return").val());

                if (!isNaN(tglMulai) && !isNaN(tglSelesai) && tglSelesai >= tglMulai) {
                    var durasi = Math.ceil((tglSelesai - tglMulai) / (1000 * 60 * 60 * 24)) + 1;
                    var totalHarga = hargaPerHari * durasi;
                    $("#total_price").val(totalHarga);
                } else {
                    $("#total_price").val("");
                }
            }
        });
    </script>
</body>

</html>