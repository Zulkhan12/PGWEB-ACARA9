<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peta dan Data Kecamatan</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Custom CSS -->
    <style>
        #map {
            width: 100%;
            height: 500px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .header {
            background-color: #343a40;
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
        }
        .table thead {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="container my-4">
        <div class="header">
            <h2>Peta dan Data Kecamatan</h2>
            <p>Data kecamatan berikut ini ditampilkan pada peta serta dalam bentuk tabel di bawah.</p>
        </div>
    </div>

    <!-- Container Utama -->
    <div class="container my-4">
        <!-- Peta -->
        <div id="map"></div>

        <!-- Tabel Data Kecamatan -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Kecamatan</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th>Luas (km²)</th>
                        <th>Jumlah Penduduk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Koneksi ke database
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "pgweb8";

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Cek jika ada request POST untuk menghapus data
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_kecamatan'])) {
                            $kecamatanToDelete = $_POST['delete_kecamatan'];

                            // Query untuk menghapus data berdasarkan nama kecamatan
                            $deleteSQL = "DELETE FROM kecamatan_data WHERE kecamatan = ?";
                            $stmt = $conn->prepare($deleteSQL);
                            $stmt->bind_param("s", $kecamatanToDelete);
                            $stmt->execute();
                            $stmt->close();

                            // Redirect untuk memperbarui halaman
                            header("Location: " . $_SERVER["PHP_SELF"]);
                            exit;
                        }

                        // Query untuk mendapatkan data kecamatan
                        $sql = "SELECT kecamatan, longitude, latitude, luas, jumlah_penduduk FROM kecamatan_data";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row["kecamatan"]) . "</td>
                                    <td>" . htmlspecialchars($row["longitude"]) . "</td>
                                    <td>" . htmlspecialchars($row["latitude"]) . "</td>
                                    <td>" . htmlspecialchars($row["luas"]) . "</td>
                                    <td>" . number_format(htmlspecialchars($row["jumlah_penduduk"])) . "</td>
                                    <td>
                                        <form method='POST' action='' onsubmit='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");' class='d-inline'>
                                            <input type='hidden' name='delete_kecamatan' value='" . htmlspecialchars($row["kecamatan"]) . "'>
                                            <button type='submit' class='btn btn-danger btn-sm'>Hapus</button>
                                        </form>
                                    </td>
                                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Tidak ada hasil ditemukan</td></tr>";
                        }

                        $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Peta JavaScript -->
    <script>
        // Inisialisasi peta
        var map = L.map("map").setView([-7.7681, 110.296], 12);

        // Tile Layer Base Map
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">Hans Sama</a> contributors',
        }).addTo(map);

        // Data Kecamatan dari PHP
        var kecamatanData = <?php
            $conn = new mysqli($servername, $username, $password, $dbname);
            $result = $conn->query($sql);
            $data = array();

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode($data);
            $conn->close();
        ?>;

        // Menambahkan marker dari data kecamatan ke peta
        kecamatanData.forEach(function(item) {
            if (item.latitude && item.longitude) {
                L.marker([item.latitude, item.longitude])
                    .addTo(map)
                    .bindPopup(
                        `<b>${item.kecamatan}</b><br>Luas: ${item.luas} km²<br>Jumlah Penduduk: ${item.jumlah_penduduk}`
                    );
            }
        });
    </script>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
