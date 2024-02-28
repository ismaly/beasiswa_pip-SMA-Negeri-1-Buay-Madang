<?php
$update = (isset($_GET['action']) && $_GET['action'] == 'update') ? true : false;
$kode_siswa = ''; // Menambahkan inisialisasi variabel $kode_siswa

if ($update) {
    $sql = $connection->query("SELECT * FROM siswa WHERE nisn='$_GET[key]'");
    $row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validasi = false;
    $err = false;

    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tahun_mengajukan = $_POST['tahun_mengajukan'];
    $periode = $_POST['periode'];

    if ($update) {
        $sql = "UPDATE siswa SET nisn='$nisn', nama='$nama', alamat='$alamat', jenis_kelamin='$jenis_kelamin', tahun_mengajukan='$tahun_mengajukan', periode='$periode' WHERE nisn='$_GET[key]'";
    } else {
        // Ambil urutan terakhir dari tabel siswa
        $sql_urutan = $connection->query("SELECT MAX(SUBSTRING(kode_siswa, 2)) AS max_urutan FROM siswa");
        $row_urutan = $sql_urutan->fetch_assoc();
        $max_urutan = $row_urutan['max_urutan'];

        // Generate Kode Siswa berikutnya
        $next_urutan = $max_urutan + 1;
        $kode_siswa = 'A' . $next_urutan;

        $sql = "INSERT INTO siswa (kode_siswa, nisn, nama, alamat, jenis_kelamin, tahun_mengajukan, periode) VALUES ('$kode_siswa', '$nisn', '$nama', '$alamat', '$jenis_kelamin', '$tahun_mengajukan', '$periode')";
        $validasi = true;
    }

    if ($validasi) {
        $q = $connection->query("SELECT nisn FROM siswa WHERE nisn='$nisn'");
        if ($q->num_rows) {
            echo alert("$nisn sudah terdaftar!", "?page=siswa");
            $err = true;
        }
    }

    if (!$err && $connection->query($sql)) {
        echo alert("Berhasil!", "?page=siswa");
    } else {
        echo alert("Gagal!", "?page=siswa");
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    // Hapus data siswa dari tabel "siswa"
    $nisn_to_delete = $_GET['key'];
    $connection->query("DELETE FROM siswa WHERE nisn='$nisn_to_delete'");

    // Hapus data nilai yang terkait dengan nisn yang dihapus
    $connection->query("DELETE FROM nilai WHERE nisn='$nisn_to_delete'");

    echo alert("Berhasil menghapus data siswa dan data nilai yang terkait.", "?page=siswa");
}
?>
<!-- Sisanya tetap sama -->


<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
	        <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
	        <div class="panel-body">
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
					<div class="form-group">
						<label for="kode_siswa">Kode Siswa</label>
						<?php if ($update): ?>
							<input type="text" name="kode_siswa" class="form-control" value="<?= $row["kode_siswa"] ?>" readonly>
							<p class="help-block">Kode Siswa tidak dapat diubah.</p>
						<?php else: ?>
							<?php
							// Ambil urutan terakhir dari tabel siswa
							$sql_urutan = $connection->query("SELECT MAX(SUBSTRING(kode_siswa, 2)) AS max_urutan FROM siswa");
							$row_urutan = $sql_urutan->fetch_assoc();
							$max_urutan = $row_urutan['max_urutan'];

							// Generate Kode Siswa berikutnya
							$next_urutan = $max_urutan + 1;
							$kode_siswa = 'A' . $next_urutan;
							?>
							<input type="text" name="kode_siswa" class="form-control" value="<?= $kode_siswa ?>" readonly>
						<?php endif; ?>
					</div>


                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input type="text" name="nisn" class="form-control" <?= (!$update) ?: 'value="'.$row["nisn"].'"' ?>>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" <?= (!$update) ?: 'value="'.$row["nama"].'"' ?>>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" name="alamat" class="form-control" <?= (!$update) ?: 'value="'.$row["alamat"].'"' ?>>
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin">
                            <option>---</option>
                            <option value="Laki-laki" <?= (!$update) ?: ($row["jenis_kelamin"] === "Laki-laki" ? 'selected' : '') ?>>Laki-laki</option>
                            <option value="Perempuan" <?= (!$update) ?: ($row["jenis_kelamin"] === "Perempuan" ? 'selected' : '') ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tahun_mengajukan">Tahun Mengajukan</label>
                        <select class="form-control" name="tahun_mengajukan">
                            <option>---</option>
                            <option value="2023" <?= (!$update) ?: ($row["tahun_mengajukan"] === "2023" ? 'selected' : '') ?>>2023</option>
                            <option value="2024" <?= (!$update) ?: ($row["tahun_mengajukan"] === "2024" ? 'selected' : '') ?>>2024</option>
                            <option value="2025" <?= (!$update) ?: ($row["tahun_mengajukan"] === "2025" ? 'selected' : '') ?>>2025</option>
                            <option value="2026" <?= (!$update) ?: ($row["tahun_mengajukan"] === "2026" ? 'selected' : '') ?>>2026</option>
                            <option value="2027" <?= (!$update) ?: ($row["tahun_mengajukan"] === "2027" ? 'selected' : '') ?>>2027</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="periode">Periode</label>
                        <select class="form-control" name="periode">
                            <option>---</option>
                            <option value="Tahap1" <?= (!$update) ?: ($row["periode"] === "Tahap1" ? 'selected' : '') ?>>Tahap 1</option>
                            <option value="Tahap2" <?= (!$update) ?: ($row["periode"] === "Tahap2" ? 'selected' : '') ?>>Tahap 2</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info btn-block">Simpan</button>
                    <a href="?page=siswa" class="btn btn-danger btn-block">Batal</a>
                </form>
	        </div>
	    </div>
	</div>
	<div class="col-md-8">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">DAFTAR SISWA</h3></div>
	        <div class="panel-body">
	            <table class="table table-condensed">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>Kode</th>
	                        <th>NISN</th>
	                        <th>Nama</th>
	                        <th>Alamat</th>
	                        <th>Jenis Kelamin</th>
	                        <th>Tahun Mengajukan</th>
	                        <th>Periode</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT * FROM siswa")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
	                            <?php
								// Pastikan 'kode' ada dalam $row sebelum mencoba mengaksesnya
								if (isset($row['kode_siswa'])) {
									echo '<td>'.$row['kode_siswa'].'</td>';
								} else {
									echo '<td></td>'; // Atau lakukan sesuatu yang sesuai jika 'kode' tidak ada
								}
								?>
	                            <td><?=$row['nisn']?></td>
	                            <td><?=$row['nama']?></td>
	                            <td><?=$row['alamat']?></td>
	                            <td><?=$row['jenis_kelamin']?></td>
	                            <td><?=$row['tahun_mengajukan']?></td>
	                            <td><?=$row['periode']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=siswa&action=update&key=<?=$row['nisn']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=siswa&action=delete&key=<?=$row['nisn']?>" class="btn btn-danger btn-xs">Hapus</a>
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