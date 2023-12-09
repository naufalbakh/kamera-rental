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
if ($role !== 'admin') {
    header("Location: dashboard_user.php"); // Redirect non-admin users
    exit();
}

// Check if form is submitted for status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input values
    $rental_id = $_POST['rental_id'];
    $new_status = $_POST['new_status'];

    // Update rental items status in the database
    $update_query = $conn->query("UPDATE rental_items SET status = '$new_status' WHERE rental_id = $rental_id");

    if ($update_query === false) {
        $error_message = "Error updating status: " . $conn->error;
    } else {
        $success_message = "Status updated successfully!";
    }
}

// Fetch rentals from the database
$rentals_query = $conn->query("SELECT rentals.*, users.username FROM rentals JOIN users ON rentals.user_id = users.user_id");
$rentals = $rentals_query->fetch_all(MYSQLI_ASSOC);

// Delete rentals
if (isset($_GET['delete_rentals'])) {
    $rental_id = $_GET['delete_rentals'];

    $conn->query("DELETE FROM rental_items WHERE rental_id='$rental_id';

    SET @max_id = (SELECT MAX(rental_id) FROM rental_items);
    
    SET @sql = CONCAT('ALTER TABLE rental_items AUTO_INCREMENT = ', @max_id + 1);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    ");
    $conn->query("DELETE FROM rentals WHERE rental_id='$rental_id';

    SET @max_id = (SELECT MAX(rental_id) FROM rentals);
    
    SET @sql = CONCAT('ALTER TABLE rentals AUTO_INCREMENT = ', @max_id + 1);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    ");
    header("Location: rentals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rentals - Rental Website</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
        <a class="navbar-brand" href="#">Rental Admin</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">Users</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="items.php">Manage Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">Manage Categories</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="rentals.php">View Rentals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="crud.php?logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Manage Rentals</h2>

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
        <div class="card">
            <div class="card-header">Rental List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-dark text-center">
                        <tr>
                            <th class="col-1 text-center">Rental ID</th>
                            <th class="col-1 text-center">User ID</th>
                            <th>Username</th>
                            <th>Rental Date</th>
                            <th>Return Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-center">
                        <?php foreach ($rentals as $rental) : ?>
                            <tr>
                                <td class="col-1"><?php echo $rental['rental_id']; ?></td>
                                <td class="col-1"><?php echo $rental['user_id']; ?></td>
                                <td><?php echo $rental['username']; ?></td>
                                <td><?php echo $rental['rental_date']; ?></td>
                                <td><?php echo $rental['return_date']; ?></td>
                                <!-- <td><?php echo $rental['status']; ?></td> -->
                                <td class="col-2">
                                    <button class="btn btn-info" data-toggle="modal" data-target="#rentalItemsModal<?php echo $rental['rental_id']; ?>">View Items</button>
                                    <a href="rentals.php?delete_rentals=<?php echo $rental['rental_id']; ?>" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a>
                                    <!-- Button to trigger the status change modal -->
                                    <!-- <button class="btn btn-primary" data-toggle="modal" data-target="#changeStatusModal<?php echo $rental['rental_id']; ?>">Change Status</button> -->
                                </td>
                            </tr>

                            <!-- Rental Items Modal -->
                            <div class="modal fade" id="rentalItemsModal<?php echo $rental['rental_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="rentalItemsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rentalItemsModalLabel">Rental Items</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="container">
                                                <div class="row mb-3">
                                                    <div class="col">
                                                        <b>Rental ID: </b>
                                                        <?php echo $rental['rental_id']; ?>
                                                        <br>
                                                        <b>Username: </b>
                                                        (<?php echo $rental['user_id']; ?>) <?php echo $rental['username']; ?>
                                                        <br>
                                                    </div>
                                                    <div class="col">
                                                        <b>Rental Date: </b>
                                                        <?php echo $rental['rental_date']; ?>
                                                        <br>
                                                        <b>Return Date: </b>
                                                        <?php echo $rental['return_date']; ?>
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <?php
                                                    // Fetch rental items for the current rental
                                                    $rental_id = $rental['rental_id'];
                                                    $rental_items_query = $conn->query("SELECT items.item_id, items.name, rental_items.status FROM rental_items JOIN items ON rental_items.item_id = items.item_id WHERE rental_items.rental_id = $rental_id");

                                                    // Check for query success
                                                    if ($rental_items_query === false) {
                                                        echo "Error in rental items query: " . $conn->error;
                                                    } else {
                                                        // Fetch results only if the query was successful
                                                        $rental_items = $rental_items_query->fetch_all(MYSQLI_ASSOC);
                                                    } ?>

                                                    <div class="col text-center border border-3 py-3">
                                                        <b>Item ID</b>
                                                        <hr>
                                                        <?php foreach ($rental_items as $rental_item) : ?>
                                                            <?php echo $rental_item['item_id']; ?>
                                                            <br>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <div class="col border border-3 py-3">
                                                        <b>Item Name</b>
                                                        <hr>
                                                        <?php foreach ($rental_items as $rental_item) : ?>
                                                            <?php echo $rental_item['name']; ?>
                                                            <br>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <div class="col border border-3 py-3">
                                                        <b>Status</b>
                                                        <hr>
                                                        <?php foreach ($rental_items as $rental_item) : ?>
                                                            <?php echo $rental_item['status']; ?>
                                                            <br>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                                <div class="row my-4 px-3">
                                                    <!-- Form for updating status -->
                                                    <form method="post" action="rentals.php">
                                                        <input type="hidden" name="rental_id" value="<?php echo $rental['rental_id']; ?>">
                                                        <label for="new_status">Ubah Status</label>
                                                        <select name="new_status" class="form-control" id="new_status">
                                                            <option value="On Rent">On Rent</option>
                                                            <option value="Taken">Taken</option>
                                                            <option value="Returned">Returned</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Change Status Modal -->
                            <div class="modal fade" id="changeStatusModal<?php echo $rental['rental_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="changeStatusModalLabel">Change Status</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Form for updating status -->
                                            <form method="post" action="rentals.php">
                                                <input type="hidden" name="rental_id" value="<?php echo $rental['rental_id']; ?>">
                                                <label for="new_status">Select new status:</label>
                                                <select name="new_status" class="form-control" id="new_status">
                                                    <option value="Ready">Ready</option>
                                                    <option value="On Rent">On Rent</option>
                                                    <option value="Taken">Taken</option>
                                                    <option value="Returned">Returned</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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