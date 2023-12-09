<?php
// categories.php
session_start();

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

// Include your database connection or CRUD operations file
include("crud.php");

// Fetch categories from the database
$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);

// Insert Category
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    $conn->query("INSERT INTO categories (category_name) VALUES ('$category_name')");
    header("Location: categories.php");
    exit();
}

// Edit Category
if (isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];

    $conn->query("UPDATE categories SET category_name='$category_name' WHERE category_id='$category_id'");
    header("Location: categories.php");
    exit();
}

// Delete Category
if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];

    $conn->query("DELETE FROM categories WHERE category_id='$category_id'");
    header("Location: categories.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Rental Website</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="items.php">Manage Items</a>
                </li>
                <li class="nav-item active">
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

    <div class="container mt-5">
        <h2>Manage Categories</h2>

        <div class="modal fade" id="categoriesModal" tabindex="-1" role="dialog" aria-labelledby="categoriesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoriesModalLabel">Add Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Add Category Form -->
                        <div class="card mb-4">
                            <div class="card-header">Add New Category</div>
                            <div class="card-body">
                                <form method="post" action="categories.php">
                                    <div class="form-group">
                                        <label for="category_name">Category Name:</label>
                                        <input type="text" class="form-control" name="category_name" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="add_category">Add Category</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category List -->
        <div class="card mb-4">
            <div class="card-header">Category List</div>
            <div class="card-body">
                <button class="btn btn-success mb-3" data-toggle="modal" data-target="#categoriesModal"><i class="fa-solid fa-list"></i> Tambah Kategori</button>
                <table class="table table-bordered">
                    <thead class="table-dark text-center">
                        <tr>
                            <th class="col-1">ID</th>
                            <th>Name</th>
                            <th class="col-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php foreach ($categories as $category) : ?>
                            <tr>
                                <td class="col-1 text-center"><?php echo $category['category_id']; ?></td>
                                <td><?php echo $category['category_name']; ?></td>
                                <td class="col-2 text-center">
                                    <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $category['category_id']; ?>"><i class="fas fa-edit"></i></button>
                                    <a href="categories.php?delete_category=<?php echo $category['category_id']; ?>" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $category['category_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Edit Category Form -->
                                            <form method="post" action="categories.php">
                                                <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                                <div class="form-group">
                                                    <label for="category_name">Category Name:</label>
                                                    <input type="text" class="form-control" name="category_name" value="<?php echo $category['category_name']; ?>" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary" name="edit_category">Save Changes</button>
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