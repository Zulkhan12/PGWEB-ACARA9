<?php
// Konfigurasi koneksi MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgweb8"; // Database yang digunakan

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fungsi untuk menghapus data jika ada request
if (isset($_POST['delete_kecamatan'])) {
    $kecamatan = $_POST['delete_kecamatan'];
    $delete_sql = "DELETE FROM kecamatan_data WHERE kecamatan = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("s", $kecamatan); 

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Data kecamatan '$kecamatan' berhasil dihapus.</p>";
    } else {
        echo "<p style='color: red;'>Gagal menghapus data kecamatan '$kecamatan'.</p>";
    }
}

// Query untuk mengambil data dari tabel 'kecamatan_data'
$sql = "SELECT kecamatan, longitude, latitude, luas, jumlah_penduduk FROM kecamatan_data";
$result = $conn->query($sql);

// Memeriksa apakah ada hasil yang dikembalikan
if ($result->num_rows > 0) {
    // Membuat header tabel dengan styling
    echo "<style>
            body {
                background-image: url('https://example.com/onepiece-background.jpg'); /* Ganti dengan URL gambar latar belakang One Piece */
                background-size: cover;
                font-family: 'Arial', sans-serif;
                color: #fff;
                text-align: center;
            }
            table {
                border-collapse: collapse;
                width: 80%;
                margin: 20px auto;
                box-shadow: 0 2px 10px rgba(0,0,0,0.5);
                background-color: rgba(0, 0, 0, 0.7);
            }
            th, td {
                padding: 12px;
                border: 1px solid #ddd;
                text-align: left;
            }
            th {
                background-color: #ffcc00; /* Warna kuning untuk header */
                color: #000;
            }
            tr:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }
            input[type='submit'] {
                background-color: #f44336;
                color: white;
                border: none;
                padding: 5px 10px;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            input[type='submit']:hover {
                background-color: #d32f2f;
            }
          </style>";

    echo "<table>
            <thead>
                <tr>
                    <th>Kecamatan</th>
                    <th>Longitude</th>
                    <th>Latitude</th>
                    <th>Luas (km²)</th>
                    <th>Jumlah Penduduk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>";

    // Output data setiap baris dengan styling dan tombol hapus
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["kecamatan"]) . "</td>
                <td>" . htmlspecialchars($row["longitude"]) . "</td>
                <td>" . htmlspecialchars($row["latitude"]) . "</td>
                <td>" . htmlspecialchars($row["luas"]) . "</td>
                <td>" . number_format(htmlspecialchars($row["jumlah_penduduk"])) . "</td>
                <td>
                    <form method='POST' action='' onsubmit='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>
                        <input type='hidden' name='delete_kecamatan' value='" . htmlspecialchars($row["kecamatan"]) . "'>
                        <input type='submit' value='Hapus'>
                    </form>
                </td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>Tidak ada hasil ditemukan</p>";
}

// Menutup koneksi
$conn->close();
?>