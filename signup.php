<?php
include "config.php";
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Periksa apakah username sudah ada
    $checkUserQuery = "SELECT * FROM users WHERE username='$username'";
    $checkUserResult = $conn->query($checkUserQuery);

    if ($checkUserResult->num_rows > 0) {
        // Username sudah ada, tampilkan peringatan
        echo '<script>alert("Username sudah digunakan. Pilih username lain."); window.location.href = "signup.php";</script>';
        exit();
    }

    // Jika username belum ada dan password valid, masukkan data pengguna ke database
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    $conn->query($sql);
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Rental Website</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                <div class="card border-0 shadow rounded-3 my-5">
                    <div class="card-body p-4 p-sm-5">
                        <div class="row">
                            <a href="index.php" class="text-decoration-none text-dark"><i class="fas fa-arrow-left"></i></a>
                        </div>
                        <h2 class="card-title text-center mb-5">Sign Up</h2>
                        <form action="signup.php" method="post">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan Username Anda">
                                <label for="floatingInput">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password Anda">
                                <label for="floatingPassword">Password</label>
                            </div>
                            <div class="form-floating mb-3" hidden>
                                <input type="text" class="form-control" id="username" name="role" value="user" required placeholder="Masukkan Role Anda">
                                <label for="role">Role</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" name="signup">Sign Up</button>
                            </div>
                            <hr class="my-4">
                            <div class="d-grid mb-2">
                                <p class="text-center">Already have an account? <a href="login.php" class="text-decoration-none">Sign in Here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>