<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once "config.php";
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Periksa apakah username dan password cocok
    $sql = "SELECT * FROM pengguna WHERE username='$username'";
    $query = $connection->query($sql);

    if ($query) {
        if ($query->num_rows > 0) {
            $data = $query->fetch_assoc();
            if (password_verify($password, $data['password'])) {
                session_start();
                $_SESSION["is_logged"] = true;
                $_SESSION["as"] = $data["role"]; // Ubah dari 'status' ke 'role'
                $_SESSION["username"] = $data["username"];
                header('location: index.php');
            } else {
                echo alert("Username / Password tidak sesuai!", "login.php");
            }
        } else {
            echo alert("Username tidak ditemukan!", "login.php");
        }
    } else {
        echo "Query error!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beasiswa</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="panel panel-info">
                    <div class="panel-heading"><h3 class="text-center">LOGIN</h3></div>
                    <div class="panel-body">
                        <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" id="username" placeholder="username" autofocus="on">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                            </div>
                            <button type="submit" class="btn btn-info btn-block">Login</button>
                            <a href="daftar.php" class="btn btn-warning btn-block">Daftar</a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</body>
</html>
