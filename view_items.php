<?php
// rentals.php
session_start();

// Include your database connection or CRUD operations file
include("crud.php");

// Check user authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Check user role
$role = $_SESSION['role'];
if ($role !== 'user') {
    header("Location: dashboard.php"); // Redirect non-admin users
    exit();
}

// Check if form is submitted for status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input values
    $rental_id = $_POST['rental_id'];

    // Update rental items status in the database
    $update_query = $conn->query("UPDATE rental_items SET status = 'On Rent' WHERE rental_id = $rental_id");

    if ($update_query === false) {
        $error_message = "Error updating status: " . $conn->error;
    } else {
        $success_message = "Status updated successfully!";
    }
}
// Mendapatkan user_id dari session
$userId = $_SESSION['user_id'];

// Kueri untuk mengambil data rentals berdasarkan user_id
$rentals_query = $conn->query("SELECT rentals.*, users.username, rental_items.status, items.name FROM rentals JOIN users ON rentals.user_id = users.user_id JOIN rental_items ON rentals.rental_id = rental_items.rental_id JOIN items ON items.item_id = rental_items.item_id WHERE rentals.user_id = $userId");

$rentals = $rentals_query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rentals - Rental Website</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="dashboard_user.php">Dashboard <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="rent_items.php">Sewa <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="view_items.php">View</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="crud.php?logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>View Rentals</h2>

        <?php
        // Display success or error message
        if (isset($success_message)) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $success_message;
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button>';
            echo '</div>';
        } elseif (isset($error_message)) {
            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
        }
        ?>

        <!-- Rental List -->
        <div class="card  mb-5">
            <div class="card-header">Rental List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Items</th>
                            <th>Rental Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php foreach ($rentals as $rental) : ?>
                            <tr>
                                <td class="col-1 text-center"><?php echo $rental['user_id']; ?></td>
                                <td><?php echo $rental['username']; ?></td>
                                <td><?php echo $rental['name']; ?></td>
                                <td><?php echo $rental['rental_date']; ?></td>
                                <td><?php echo $rental['return_date']; ?></td>
                                <td><?php echo $rental['status']; ?></td>
                            </tr>

                            <!-- Rental Items Modal -->
                            <div class="modal fade" id="rentalItemsModal<?php echo $rental['rental_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="rentalItemsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rentalItemsModalLabel">View Items</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <!-- Form for updating status -->
                                        <form method="post" action="view_items.php">
                                            <div class="modal-body">
                                                <input type="hidden" name="rental_id" value="<?php echo $rental['rental_id']; ?>">
                                                <h4>Anda Yakin Akan Mengambil Barang?</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Yakin</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button class="btn btn-info" data-toggle="modal" data-target="#rentalItemsModal<?php echo $rental['rental_id']; ?>"><i class="fas fa-edit"></i>Ubah Status</button>
            </div>
        </div>
    </div>
    <!-- Footer-->
    <footer class="py-3 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Naufal Bakhtiar Ismail 2023</p>
        </div>
    </footer>

    <script>
        // Auto close the success message after 3 seconds
        setTimeout(function() {
            $(".alert").alert('close');
        }, 3000);
    </script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>

</html>