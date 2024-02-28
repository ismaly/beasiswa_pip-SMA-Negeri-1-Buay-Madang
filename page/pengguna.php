<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
    $sql = $connection->query("SELECT * FROM pengguna WHERE kd_pengguna='$_GET[key]'");
    $row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validasi = false;
    $err = false;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($update) {
        $sql = "UPDATE pengguna SET username='$username', password='$password', role='$role' WHERE kd_pengguna='$_GET[key]'";
    } else {
        $sql = "INSERT INTO pengguna (username, password, role) VALUES ('$username', '$password', '$role')";
        $validasi = true;
    }

    if ($validasi) {
        $q = $connection->query("SELECT kd_pengguna FROM pengguna WHERE username='$username'");
        if ($q->num_rows) {
            echo alert("Username sudah digunakan. Silakan pilih username lain.", "?page=pengguna");
            $err = true;
        }
    }

    if (!$err AND $connection->query($sql)) {
        echo alert("Berhasil!", "?page=pengguna");
    } else {
        echo alert("Gagal!", "?page=pengguna");
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
            <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
            <div class="panel-body">
                <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= ($update) ? $row['username'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" value="<?= ($update) ? $row['password'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="role">Peran</label>
                        <select name="role" class="form-control" id="role">
                            <option>-----</option>
                            <option value="siswa" <?= ($update && $row['role'] === 'siswa') ? 'selected' : '' ?>>Siswa</option>
                            <option value="petugas" <?= ($update && $row['role'] === 'petugas') ? 'selected' : '' ?>>Petugas</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
                    <?php if ($update): ?>
						<a href="?page=pengguna" class="btn btn-info btn-block">Batal</a>
					<?php endif; ?>
                </form>
            </div>
        </div>
    </div>

	<div class="col-md-8">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">DAFTAR PENGGUNA SISTEM </h3></div>
	        <div class="panel-body">
	            <table class="table table-condensed">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>Username</th>
	                        <th>Password</th>
	                        <th>Role</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT * FROM pengguna")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
	                            <td><?=$row['username']?></td>
	                            <td><?=$row['password']?></td>
	                            <td><?=$row['role']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=pengguna&action=update&key=<?=$row['kd_pengguna']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=pengguna&action=delete&key=<?=$row['kd_pengguna']?>" class="btn btn-danger btn-xs">Hapus</a>
	                                </div>
	                            </td>
	                        </tr>
	                        <?php endwhile ?>
	                    <?php endif ?>
	                </tbody>
	            </table>
	        </div>
	    </div>
	</div>
</div>
