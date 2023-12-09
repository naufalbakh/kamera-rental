<?php
include("crud.php");

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rental Website</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
        <a class="navbar-brand" href="dashboard.php">Rental Admin</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Dashboard <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="items.php">Manage Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">Manage Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="rentals.php">View Rentals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="crud.php?logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid mt-5 mb-5 px-4 py-4">
        <h2 class="text-center">Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <div id="real-time-clock" class="text-center mt-3"></div>

        <!-- Admin Features -->
        <div class="row mt-4">
            <div class="col-6">
                <div class="card text-center h-100">
                    <h4 class="card-header">Katalog</h4>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Sertakan koneksi database

                            // Ambil katalog items langsung dari database
                            $katalogResult = $conn->query("SELECT items.*, categories.category_name FROM items JOIN categories ON items.category_id = categories.category_id ORDER BY category_id");

                            // Periksa apakah kueri berhasil dijalankan
                            if ($katalogResult === false) {
                                die("Error executing query: " . $conn->error);
                            }

                            // Periksa apakah ada hasil yang dikembalikan
                            if ($katalogResult->num_rows > 0) {
                                while ($katalogRow = $katalogResult->fetch_assoc()) {
                            ?>
                                    <div class="col-4 mb-4">
                                        <div class="card h-100">
                                            <img src="<?php echo $katalogRow['image']; ?>" class="card-img-top" alt="<?php echo $katalogRow['name']; ?>">
                                            <div class="card-body">
                                                <h6 class="card-title mb-4"><?php echo $katalogRow['name']; ?></h6>
                                                <p class="card-subtitle py-2 text-secondary"><?php echo $katalogRow['category_name']; ?></p>
                                                <ul class="list-group list-group-flush ">
                                                    <li class="list-group-item py-1">
                                                        <p class="card-text ">Rp. <?php echo number_format($katalogRow['price'], 0, ',', '.'); ?></p>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-footer">
                                                <a href="items.php" class="btn btn-primary">Kelola</a>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                echo "Tidak ada item dalam katalog.";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <!-- Rent Process -->
                <div class="card text-center h-100">
                    <h4 class="card-header">Item yang Sedang Disewa</h4>
                    <div class="card-body">
                        <!-- Formulir untuk proses sewa -->
                        <!-- ... -->
                        <div class="row">
                            <?php

                            // Ambil item yang sedang direntalkan langsung dari database
                            $result = $conn->query("SELECT * FROM rental_items JOIN items ON rental_items.item_id = items.item_id JOIN rentals ON rental_items.rental_id=rentals.rental_id JOIN users ON rentals.user_id=users.user_id");

                            // Periksa apakah kueri berhasil dijalankan
                            if ($result === false) {
                                die("Error executing query: " . $conn->error);
                            }

                            // Periksa apakah ada hasil yang dikembalikan
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <div class="col-4 mb-4">
                                        <div class="card h-100">
                                            <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo $row['name']; ?></h6>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
                                                        <p>Return: </p>
                                                        <p class="card-text text-danger font-weight-bold"> <?php echo date('l, d-F-Y', strtotime($row['return_date'])); ?></p>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-footer">
                                                <p class="card-text">Penyewa: <a href="rentals.php" class="text-info text-decoration-none"><?php echo $row['username']; ?></a> </p>
                                                <!-- Tambahkan informasi lain jika diperlukan -->
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                echo "Tidak ada item yang sedang direntalkan.";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer-->
    <footer class="py-3 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Naufal Bakhtiar Ismail 2023</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        function updateClock() {
            var now = new Date();

            // Mendapatkan informasi tanggal
            // var day = now.toLocaleDateString('id-ID', {
            //     weekday: 'long'
            // });
            var date = now.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            // Mendapatkan informasi jam
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();

            // Format jam dengan dua digit
            hours = (hours < 10 ? "0" : "") + hours;
            minutes = (minutes < 10 ? "0" : "") + minutes;
            seconds = (seconds < 10 ? "0" : "") + seconds;

            // Tampilkan waktu dalam format HH:MM:SS
            var timeString = hours + ':' + minutes + ':' + seconds;

            // Tampilkan tanggal dan hari
            var dateTimeString = date + ' - ' + timeString;

            // Update elemen dengan id "real-time-clock"
            document.getElementById('real-time-clock').textContent = dateTimeString;
        }

        // Panggil updateClock setiap detik
        setInterval(updateClock, 1000);
    </script>

</body>

</html>