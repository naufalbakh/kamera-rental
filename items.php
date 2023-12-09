<?php
// items.php
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

// Fetch items and categories from the database
$items = $conn->query("SELECT items.*, categories.category_name FROM items JOIN categories ON items.category_id = categories.category_id ORDER BY item_id")->fetch_all(MYSQLI_ASSOC);
$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);

// Insert Item
if (isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Periksa apakah file dipilih
    if ($_FILES['image']['size'] > 0) {
        // Kode tambahan untuk mengatur ukuran file maksimum
        $max_file_size = 5000000; // 5MB dalam byte

        if ($_FILES['image']['size'] > $max_file_size) {
            echo '<script>alert("Ukuran gambar lebih dari 5Mb"); window.location.href = "items.php";</script>';
            exit();
        }

        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Buat direktori "uploads" jika belum ada
        if (!file_exists($targetDir)) {
            mkdir($targetDir);
        }

        // Pindahkan file yang diunggah ke direktori target
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Simpan informasi item (termasuk jalur gambar) ke database
            $conn->query("INSERT INTO items (name, description, price, category_id, image) VALUES ('$name', '$description', '$price', '$category_id', '$targetFile')");
            header("Location: items.php");
            exit();
        } else {
            echo "Maaf, terjadi kesalahan saat mengunggah file Anda.";
        }
    } else {
        // Tangani kasus ketika tidak ada file yang dipilih
        echo "Silakan pilih file gambar.";
    }
}


// Edit Item
if (isset($_POST['edit_item'])) {
    $item_id = $_POST['item_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Upload image if a new image is selected
    if ($_FILES["image"]["size"] > 0) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $conn->query("UPDATE items SET name='$name', description='$description', price='$price', category_id='$category_id', image='$targetFile' WHERE item_id='$item_id'");
            header("Location: items.php");
            exit();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        // No new image selected, update other information
        $conn->query("UPDATE items SET name='$name', description='$description', price='$price', category_id='$category_id' WHERE item_id='$item_id'");
        header("Location: items.php");
        exit();
    }
}

// Delete Item
if (isset($_GET['delete_item'])) {
    $item_id = $_GET['delete_item'];

    $conn->query("DELETE FROM items WHERE item_id='$item_id'");

    header("Location: items.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items - Rental Website</title>
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
                <li class="nav-item active">
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

    <div class="container mt-5">
        <h2>Manage Items</h2>

        <div class="modal fade" id="itemsModal" tabindex="-1" role="dialog" aria-labelledby="itemsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="itemsModalLabel">Add Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Add Item Form -->
                        <div class="card mb-4">
                            <div class="card-header">Add New Item</div>
                            <div class="card-body">
                                <form method="post" action="items.php" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="name">Name:</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description:</label>
                                        <textarea class="form-control" name="description" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price:</label>
                                        <input type="number" class="form-control" name="price" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Category:</label>
                                        <select class="form-control" name="category_id" required>
                                            <?php foreach ($categories as $category) : ?>
                                                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Image:</label>
                                        <input type="file" class="form-control" name="image" accept="image/*" placeholder="Silahkan Upload Gambar" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="add_item">Add Item</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Item List -->
        <div class="card mb-4">
            <div class="card-header">Item List</div>
            <div class="card-body">
                <button class="btn btn-success mb-3" data-toggle="modal" data-target="#itemsModal"><i class="fa-solid fa-icons"></i> Tambah Item</button>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Gambar</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?php echo $item['item_id']; ?></td>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['description']; ?></td>
                                <td>Rp. <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td><?php echo $item['category_name']; ?></td>
                                <td>
                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" style="max-width: 150px; max-height: 150px;" class="rounded">
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $item['item_id']; ?>">
                                            <i class="fas fa-edit"></i> <!-- Ikon Edit -->
                                        </button>
                                        <a href="items.php?delete_item=<?php echo $item['item_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">
                                            <i class="fas fa-trash-alt"></i> <!-- Ikon Delete -->
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $item['item_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Item</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Edit Item Form -->
                                            <form method="post" action="items.php" enctype="multipart/form-data">
                                                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                                <div class="form-group">
                                                    <label for="name">Name:</label>
                                                    <input type="text" class="form-control" name="name" value="<?php echo $item['name']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description:</label>
                                                    <textarea class="form-control" name="description" required><?php echo $item['description']; ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="price" class="mr-2">Price:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="addon-wrapping">Rp.</span>
                                                        <input type="number" class="form-control" name="price" value="<?php echo $item['price']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="category">Category:</label>
                                                    <select class="form-control" name="category_id" required>
                                                        <?php foreach ($categories as $category) : ?>
                                                            <option value="<?php echo $category['category_id']; ?>" <?php echo ($category['category_id'] == $item['category_id']) ? 'selected' : ''; ?>><?php echo $category['category_name']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="image">Image:</label>
                                                    <input type="file" class="form-control" name="image" accept="image/*">
                                                </div>

                                                <button type="submit" class="btn btn-primary" name="edit_item">Save Changes</button>
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