<?php
include("config.php"); // Include database connection

// Add User
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Periksa apakah username sudah ada
    $checkUserQuery = "SELECT * FROM users WHERE username='$username'";
    $checkUserResult = $conn->query($checkUserQuery);

    if ($checkUserResult->num_rows > 0) {
        // Username sudah ada, tampilkan peringatan
        echo '<script>alert("Username sudah digunakan. Pilih username lain."); window.location.href = "users.php";</script>';
        exit();
    }

    // Jika username belum ada dan password valid, masukkan data pengguna ke database
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    $conn->query($sql);
    header("Location: users.php");
    exit();
}



// Get Users
function getUsers()
{
    global $conn;
    $result = $conn->query("SELECT * FROM users");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Edit User
if (isset($_GET['edit_user'])) {
    $user_id = $_GET['edit_user'];
    $result = $conn->query("SELECT * FROM users WHERE user_id=$user_id");
    $user = $result->fetch_assoc();
}


// Update User
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['edit_username'];
    $password = $_POST['edit_password']; // Perlu penanganan keamanan lebih lanjut, seperti hashing
    $role = $_POST['edit_role'];

    $sql = "UPDATE users SET username='$username', password='$password', role='$role' WHERE user_id=$user_id";
    $conn->query($sql);
    header("Location: users.php");
    exit();
}

// Delete User
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE user_id=$id");
    header("Location: users.php");
    exit();
}

// Login Pengguna
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    $user = $result->fetch_assoc();

    if ($user) {
        // Periksa apakah password benar
        if ($password === $user['password']) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Arahkan ke dashboard yang sesuai berdasarkan peran
            if ($user['role'] == 'user') {
                header("Location: dashboard_user.php");
            } elseif ($user['role'] == 'admin') {
                header("Location: dashboard.php");
            } else {
                // Peran tidak valid
                echo '<script>alert("Login gagal. Hubungi admin."); window.location.href = "login.php";</script>';
                exit();
            }
            exit();
        } else {
            echo '<script>alert("Login gagal. Password salah."); window.location.href = "login.php";</script>';
            exit();
        }
    } else {
        echo '<script>alert("Login gagal. Pengguna tidak ditemukan."); window.location.href = "login.php";</script>';
        exit();
    }
}



// Logout User
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location: index.php");
    exit();
}
