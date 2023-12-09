<?php
include("crud.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Website</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
        <a class="navbar-brand" href="dashboard.php">Rental Kamera</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Dashboard <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block" src="img/img1.png" alt="First slide">
            </div>
            <div class="carousel-item">
                <img class="d-block" src="img/img2.png" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img class="d-block" src="img/img3.png" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="container mt-5">

        <!-- Your E-Commerce Content Goes Here -->
        <h3 class="text-center">Available Items for Rent</h3>
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
                                <div class="col-4 mb-4 px-2">
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
                                            <a href="login.php" class="btn btn-primary">Sewa</a>
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
        <!-- ... -->
    </div>
    <!-- Footer-->
    <footer class="py-3 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Naufal Bakhtiar Ismail 2023</p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>