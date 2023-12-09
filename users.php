<!-- users.php -->
<?php
include("crud.php"); // Include CRUD operations

// Read Users
$users = getUsers();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Rental Website</title>
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
                    <a class="nav-link active" href="users.php">Users</a>
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
    <div class="container mt-5">
        <h2>Users</h2>
        <!-- Rental Items Modal -->
        <div class="modal fade" id="usersModal" tabindex="-1" role="dialog" aria-labelledby="usersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usersModalLabel">Add User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Add User Form -->
                        <form action="crud.php" method="post">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="role">Role:</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="card mt-4 mb-4">
            <div class="card-header">Users List</div>
            <div class="card-body">
                <button class="btn btn-success mb-3" data-toggle="modal" data-target="#usersModal"><i class="fas fa-user"></i> Tambah User</button>
                <!-- Users Table -->
                <table class="table table-bordered">
                    <thead class="table-dark text-center">
                        <tr>
                            <th class="col-1">User ID</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th class="col-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-center">
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td class="col-1"><?php echo $user['user_id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['password']; ?></td>
                                <td><?php echo $user['role']; ?></td>
                                <td class="col-2">
                                    <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $user['user_id']; ?>">
                                        <i class="fas fa-edit"></i> <!-- Ikon Edit -->
                                    </button>
                                    <!-- <a href="edit_user.php?edit_user=<?php echo $user['user_id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a> -->
                                    <a href="crud.php?delete_user=<?php echo $user['user_id']; ?>" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
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