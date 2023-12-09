<?php
// Include the database configuration
include 'config.php';

// Start the session
session_start();

// Initialize variables
$confirmationMessage = '';
$totalPrice = 0;
$rentedItems = [];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page or handle accordingly
    header("Location: login.php");
    exit();
}

// Get user-specific information from session
$userId = $_SESSION['user_id']; // Assume 'user_id' is the key used when storing user ID in session

// Periksa apakah formulir dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dapatkan item yang dipilih dan tanggal dari formulir
    $selectedItems = isset($_POST['selectedItems']) ? $_POST['selectedItems'] : [];
    $rentalDate = isset($_POST['rentalDate']) ? $_POST['rentalDate'] : date('Y-m-d');
    $returnDate = isset($_POST['returnDate']) ? $_POST['returnDate'] : date('Y-m-d', strtotime('+7 days'));

    // Validasi dan proses peminjaman
    if (!empty($selectedItems)) {

        foreach ($selectedItems as $itemId) {
            $result = mysqli_query($conn, "SELECT price FROM items WHERE item_id = '$itemId'");
            $itemData = mysqli_fetch_assoc($result);
            $itemPrice = $itemData['price'];
            $totalPrice += $itemPrice;
        }
        // Masukkan data ke dalam tabel rentals
        mysqli_query($conn, "INSERT INTO rentals (user_id, rental_date, return_date) VALUES ('$userId', '$rentalDate', '$returnDate')");

        // Dapatkan ID peminjaman terakhir yang dimasukkan
        $rentalId = mysqli_insert_id($conn);

        // Masukkan data ke dalam tabel rental_items
        foreach ($selectedItems as $itemId) {
            mysqli_query($conn, "INSERT INTO rental_items (rental_id, item_id, status) VALUES ('$rentalId', '$itemId', 'Taken')");
        }

        // Ambil item yang dipinjam untuk ditampilkan setelah konfirmasi
        $rentedItemsResult = mysqli_query($conn, "SELECT items.name, items.description, items.price FROM rental_items JOIN items ON rental_items.item_id = items.item_id WHERE rental_items.rental_id = '$rentalId'");
        $rentedItems = mysqli_fetch_all($rentedItemsResult, MYSQLI_ASSOC);

        $confirmationMessageRentals = 'Peminjaman berhasil!';
        $confirmationMessageRentalItems = 'Item yang dipilih:';
    }
}

// Get items from the database
$itemResult = mysqli_query($conn, "SELECT * FROM items");
$items = mysqli_fetch_all($itemResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Camera Equipment</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
        <a class="navbar-brand" href="dashboard_user.php">Rental <?php echo $_SESSION['username']; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard_user.php">Dashboard <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="rent_items.php">Sewa <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_items.php">View</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="crud.php?logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Sewa Items</h2>
        <div class="card mb-5">
            <div class="card-header">Item List</div>
            <div class="card-body">
                <div class="container mb-4">
                    <?php if (!empty($confirmationMessage)) : ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $confirmationMessage; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" id="rentalForm">
                        <div class="row mb-3 align-middle">
                            <div class="col-2">
                                Tanggal Sewa: <br><input type="date" name="rentalDate" id="rentalDate" value="<?php echo $rentalDate; ?>" required>
                            </div>
                            <div class="col-3">
                                Tanggal Kembali: <br><input type="date" name="returnDate" id="returnDate" value="<?php echo $returnDate; ?>" required>
                            </div>
                        </div>


                        <table class="table table table-bordered">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Pilih</th>
                                    <th>Item ID</th>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php foreach ($items as $item) : ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="selectedItems[]" value="<?php echo $item['item_id']; ?>">
                                        </td>
                                        <td class="text-center"><?php echo $item['item_id']; ?></td>
                                        <td><?php echo $item['name']; ?></td>
                                        <td><?php echo $item['description']; ?></td>
                                        <td>Rp. <?php echo number_format($item['price'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                        <?php if (empty($confirmationMessage)) : ?>
                            <button type="submit" class="btn btn-success mb-3">Confirm Rental</button>
                        <?php endif; ?>
                    </form>
                </div>


                <?php if (!empty($confirmationMessageRentalItems)) : ?>
                    <div class="container mb-4">
                        <div class="alert alert-info" role="alert">
                            <?php echo $confirmationMessageRentalItems; ?>
                        </div>

                        <table class="table table-bordered">
                            <!-- Tabel item yang dipilih -->
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php foreach ($rentedItems as $rentedItem) : ?>
                                    <tr>
                                        <td><?php echo $rentedItem['name']; ?></td>
                                        <td><?php echo $rentedItem['description']; ?></td>
                                        <td>Rp. <?php echo number_format($rentedItem['price'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <!-- <td colspan="2"></td> -->
                                    <td colspan="2"><strong>Total Price:</strong></td>
                                    <td>Rp. <?php echo number_format($totalPrice, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>

                        <a href="view_items.php" class="btn btn-info">View Items</a>
                    </div>


                <?php endif; ?>
            </div>
        </div>


    </div>
    <!-- Footer-->
    <footer class="py-3 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Naufal Bakhtiar Ismail 2023</p>
        </div>
    </footer>

    <!-- Include Bootstrap and jQuery JS scripts here -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <!-- Add your JavaScript logic here if needed -->

</body>

</html>