<?php
include("crud.php");
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Check user role
$role = $_SESSION['role'];
if ($role !== 'user') {
    header("Location: index.php"); // Redirect non-admin users
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Website</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
        <a class="navbar-brand" href="dashboard_user.php">Rental <?php echo $_SESSION['username']; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard_user.php">Dashboard <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="rent_items.php">Sewa <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="view_items.php">View</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="crud.php?logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Welcome, <?php echo $_SESSION['username']; ?>!</h2>

        <!-- Your E-Commerce Content Goes Here -->
        <div class="row">
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
                                            <a href="rent_items.php" class="btn btn-primary">Sewa Sekarang</a>
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
        <!-- Tampilkan item-item yang siap disewa -->
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
</body>

</html>