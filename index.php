<?php
// Fungsi untuk menampilkan header
function displayHeader() {
    echo '<header>
        <div class="logo">
            <img src="Logo.png" alt="Baby Island">
            <span>Baby Island</span>
        </div>
        <nav class="navbar">
            <a href="#Home">Home</a>
            <a href="#How to Order">How to Order</a>
            <a href="#Category">Category</a>
            <a href="#Login">Login</a>
        </nav>
    </header>';
}

// Inisialisasi variabel untuk menyimpan nilai input dan error
$nama = $email = $nomor = $mobil = $alamat = $tanggal_mulai = $tanggal_selesai = $lamaSewa = "";
$namaErr = $emailErr = $nomorErr = $alamatErr = $tanggalErr = "";

// Mengecek apakah form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi Nama
    $nama = $_POST["nama"];
    if (empty($nama)) {
        $namaErr = "Nama wajib diisi";
    }

    // Validasi Email
    $email = $_POST["email"];
    if (empty($email)) {
        $emailErr = "Email wajib diisi";
    }

    // Validasi Nomor Telepon
    $nomor = $_POST["nomor"];
    if (empty($nomor)) {
        $nomorErr = "Nomor Telepon wajib diisi";
    } elseif (!ctype_digit($nomor)) {
        $nomorErr = "Nomor Telepon harus berupa angka";
    }

    // Validasi Alamat
    $alamat = $_POST["alamat"];
    if (empty($alamat)) {
        $alamatErr = "Alamat wajib diisi";
    }

    // Menyimpan pilihan mobil tanpa validasi khusus
    $mobil = $_POST["mobil"];

    // Validasi dan Hitung Lama Sewa
    $tanggal_mulai = $_POST["tanggal_mulai"];
    $tanggal_selesai = $_POST["tanggal_selesai"];

    if (empty($tanggal_mulai) || empty($tanggal_selesai)) {
        $tanggalErr = "Tanggal wajib diisi";
    } elseif ($tanggal_mulai > $tanggal_selesai) {
        $tanggalErr = "Tanggal mulai tidak boleh lebih besar dari tanggal selesai!";
    } else {
        // Hitung lama sewa dalam hari
        $date1 = new DateTime($tanggal_mulai);
        $date2 = new DateTime($tanggal_selesai);
        $interval = $date1->diff($date2);
        $lamaSewa = $interval->days + 1; // +1 agar hari pertama juga dihitung
    }

    // Redirect untuk mengosongkan form
    if (!$namaErr && !$emailErr && !$nomorErr && !$alamatErr && !$tanggalErr) {
        // Simpan data
        session_start();
        $_SESSION['data_sewa'] = [
            'nama' => $nama,
            'email' => $email,
            'nomor' => $nomor,
            'mobil' => $mobil,
            'alamat' => $alamat,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'lamaSewa' => $lamaSewa
        ];

        // bersih form
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    }
}

// Ambil data sewa dari session jika ada
session_start();
$dataSewa = $_SESSION['data_sewa'] ?? null;
unset($_SESSION['data_sewa']); // Hapus session setelah ditampilkan
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Sewa Perlengkapan Bayi</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php displayHeader(); ?> <!-- Menampilkan header -->

    <div class="container">
        <h2>Form Sewa Perlengkapan Bayi</h2>
        <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama">
                <span class="error"><?php echo $namaErr ? "* $namaErr" : ""; ?></span>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email">
                <span class="error"><?php echo $emailErr ? "* $emailErr" : ""; ?></span>
            </div>

            <div class="form-group">
                <label for="nomor">Nomor Telepon:</label>
                <input type="text" id="nomor" name="nomor">
                <span class="error"><?php echo $nomorErr ? "* $nomorErr" : ""; ?></span>
            </div>

            <div class="form-group">
                <label for="mobil">Pilih Perlengkapan:</label>
                <select id="mobil" name="mobil">
                    <option value="Baby Chair">Baby Chair</option>
                    <option value="Stroller">Stroller</option>
                    <option value="Box Baby">Box Baby</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_mulai">Dari Tanggal:</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai">

                <label for="tanggal_selesai">Sampai Tanggal:</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai">

                <span class="error"><?php echo $tanggalErr ? "* $tanggalErr" : ""; ?></span>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat Pengiriman:</label>
                <textarea id="alamat" name="alamat"></textarea>
                <span class="error"><?php echo $alamatErr ? "* $alamatErr" : ""; ?></span>
            </div>

            <div class="button-container">
                <button type="submit">Sewa</button>
            </div>
        </form>
    </div>

    <?php if ($dataSewa) { ?>
    <div class="container">
        <h3>Data Sewa:</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Perlengkapan</th>
                        <th>Alamat Pengiriman</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Lama Sewa (Hari)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $dataSewa['nama']; ?></td>
                        <td><?php echo $dataSewa['email']; ?></td>
                        <td><?php echo $dataSewa['nomor']; ?></td>
                        <td><?php echo $dataSewa['mobil']; ?></td>
                        <td><?php echo $dataSewa['alamat']; ?></td>
                        <td><?php echo $dataSewa['tanggal_mulai']; ?></td>
                        <td><?php echo $dataSewa['tanggal_selesai']; ?></td>
                        <td><?php echo $dataSewa['lamaSewa']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
</body>

</html>
