<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once "config.php";

    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Menambahkan peran yang dipilih oleh pengguna

    // Periksa apakah username sudah ada di database
    $checkUsernameQuery = "SELECT * FROM pengguna WHERE username = '$username'";
    $checkUsernameResult = $connection->query($checkUsernameQuery);

    if ($checkUsernameResult->num_rows > 0) {
        echo alert("Username sudah digunakan. Silakan pilih username lain.", "daftar.php");
    } else {
        // Tambahkan data pendaftar ke database dengan peran yang dipilih
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO pengguna (username, password, role) VALUES ('$username', '$hashedPassword', '$role')";

        if ($connection->query($insertQuery)) {
            echo alert("Pendaftaran berhasil. Silakan login.", "login.php");
        } else {
            echo alert("Pendaftaran gagal.", "daftar.php");
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beasiswa - Pendaftaran</title>
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
                    <div class="panel-heading"><h3 class="text-center">PENDAFTARAN</h3></div>
                    <div class="panel-body">
                        <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" id="username" placeholder="Username" autofocus="on">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label for="role">Peran</label>
                                <select name="role" class="form-control" id="role">
                                    <option>-----</option>
                                    <option value="siswa">Siswa</option>
                                    <option value="petugas">Petugas</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning btn-block">Daftar</button>
                            <a href="login.php" class="btn btn-info btn-block">Login</a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</body>
</html>
