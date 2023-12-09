<!-- edit_user.php -->
<?php
include("crud.php"); // Include CRUD operations

// Ambil data pengguna yang akan diedit
if (isset($_GET['edit_user'])) {
    $user_id = $_GET['edit_user'];
    $result = $conn->query("SELECT * FROM users WHERE user_id=$user_id");
    $user = $result->fetch_assoc();
} else {
    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Rental Website</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <h2>Edit User</h2>

        <!-- Edit User Form -->
        <form action="crud.php" method="post">
            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
            <div class="form-group">
                <label for="edit_username">Username:</label>
                <input type="text" class="form-control" id="edit_username" name="edit_username" value="<?php echo $user['username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="edit_password">Password:</label>
                <input type="password" class="form-control" id="edit_password" name="edit_password" value="<?php echo $user['password']; ?>" required>
            </div>
            <div class="form-group">
                <label for="edit_role">Role:</label>
                <select class="form-control" id="edit_role" name="edit_role">
                    <option value="User" <?php echo ($user['role'] == 'User') ? 'selected' : ''; ?>>User</option>
                    <option value="Admin" <?php echo ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success" name="update_user">Update User</button>
        </form>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal<?php echo $user['user_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Edit Item Form -->
                    <form action="crud.php" method="post">
                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                        <div class="form-group">
                            <label for="edit_username">Username:</label>
                            <input type="text" class="form-control" id="edit_username" name="edit_username" value="<?php echo $user['username']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_password">Password:</label>
                            <input type="password" class="form-control" id="edit_password" name="edit_password" value="<?php echo $user['password']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_role">Role:</label>
                            <select class="form-control" id="edit_role" name="edit_role">
                                <option value="User" <?php echo ($user['role'] == 'User') ? 'selected' : ''; ?>>User</option>
                                <option value="Admin" <?php echo ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" name="update_user">Update User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>