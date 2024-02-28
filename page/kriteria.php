<?php
$update = (isset($_GET['action']) && $_GET['action'] == 'update') ? true : false;
$selectedBeasiswa = '';
$kd_kriteria = '';
$kd_beasiswa = '';
$sifat = '';
$bobot = '';

if ($update) {
    if (!empty($_GET['key'])) {
        $sql = $connection->query("SELECT * FROM kriteria WHERE kd_kriteria='" . $_GET['key'] . "'");
        $row = $sql->fetch_assoc();
        $selectedBeasiswa = $row['kd_beasiswa'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validasi = false;
    $err = false;

    if ($update) {
        $sql = "UPDATE kriteria SET kd_beasiswa = '$_POST[kd_beasiswa]', nama = '$_POST[nama]', sifat = '$_POST[sifat]', bobot = '$_POST[bobot]' WHERE kd_kriteria = '$_GET[key]'";
    } else {
        $sql_urutan = $connection->query("SELECT MAX(SUBSTRING(kd_kriteria, 2)) AS max_urutan FROM kriteria WHERE kd_beasiswa = '$_POST[kd_beasiswa]'");
        $row_urutan = $sql_urutan->fetch_assoc();
        $max_urutan = $row_urutan['max_urutan'];

        $next_urutan = $max_urutan + 1;
        $kd_kriteria = 'C' . $next_urutan;
        $kd_beasiswa = $_POST['kd_beasiswa'];

        $sql = "INSERT INTO kriteria (kd_kriteria, kd_beasiswa, nama, sifat, bobot) VALUES ('$kd_kriteria', '$kd_beasiswa', '$_POST[nama]', '$_POST[sifat]', '$_POST[bobot]')";
        $validasi = true;
    }

    if ($validasi) {
        $q = $connection->query("SELECT kd_kriteria FROM kriteria WHERE kd_beasiswa = '$_POST[kd_beasiswa]' AND nama LIKE '%$_POST[nama]%'");
        if ($q->num_rows) {
            echo alert("Kriteria sudah ada!", "?page=kriteria");
            $err = true;
        }
    }

    if (!$err) {
        if ($connection->query($sql)) {
            echo alert("Berhasil!", "?page=kriteria");
        } else {
            echo alert("Gagal!", "?page=kriteria");
        }
    } else {
        echo alert("Gagal!", "?page=kriteria");
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $connection->query("DELETE FROM kriteria WHERE kd_kriteria = '$_GET[key]'");
    echo alert("Berhasil!", "?page=kriteria");
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
            <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
            <div class="panel-body">
                <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
                    <div class="form-group">
                        <label for="kd_beasiswa">Beasiswa</label>
                        <select class="form-control" name="kd_beasiswa" <?= ($update) ? 'disabled' : '' ?>>
                            <option>Pilih Beasiswa</option>
                            <?php
                            $sql_beasiswa = $connection->query("SELECT * FROM beasiswa");
                            while ($data_beasiswa = $sql_beasiswa->fetch_assoc()):
                                $kd_beasiswa_option = $data_beasiswa["kd_beasiswa"];
                                $nama_beasiswa_option = $data_beasiswa["nama_beasiswa"];
                            ?>
                                <option value="<?= $kd_beasiswa_option ?>" <?= ($kd_beasiswa_option == $selectedBeasiswa) ? 'selected' : '' ?>>
                                    <?= $nama_beasiswa_option ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- Tampilkan field Kode Kriteria, Nama, Sifat, dan Bobot -->
                    <div class="form-group">
						<label for="kd_kriteria">Kode Siswa</label>
						<?php if ($update): ?>
							<input type="text" name="kd_kriteria" class="form-control" value="<?= $row["kd_kriteria"] ?>" readonly>
							<p class="help-block">Kode kriteria tidak dapat diubah</p>
						<?php else: ?>
							<?php
							// Ambil urutan terakhir dari tabel siswa
							$sql_urutan = $connection->query("SELECT MAX(SUBSTRING(kd_kriteria, 2)) AS max_urutan FROM kriteria");
							$row_urutan = $sql_urutan->fetch_assoc();
							$max_urutan = $row_urutan['max_urutan'];

							// Generate Kode Siswa berikutnya
							$next_urutan = $max_urutan + 1;
							$kd_kriteria = 'C' . $next_urutan;
							?>
							<input type="text" name="kd_kriteria" class="form-control" value="<?= $kd_kriteria ?>" readonly>
						<?php endif; ?>
					</div>
                    <div class="form-group">
                        <label for="nama">Nama Kriteria</label>
                        <input type="text" name="nama" class="form-control" value="<?= ($update) ? $row['nama'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="sifat">Sifat</label>
                        <select class="form-control" name="sifat">
                            <option>---</option>
                            <option value="cost" <?= ($row["sifat"] == "cost") ? 'selected' : '' ?>>Cost</option>
                            <option value="benefit" <?= ($row["sifat"] == "benefit") ? 'selected' : '' ?>>Benefit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bobot">Bobot</label>
                        <input type="text" name="bobot" class="form-control" value="<?= ($update) ? $row['bobot'] : '' ?>">
                    </div>

                    <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
                    <?php if ($update): ?>
                        <a href="?page=kriteria" class="btn btn-info btn-block">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="panel panel-info">
            <div class="panel-heading"><h3 class="text-center">DAFTAR KRITERIA</h3></div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="kd_beasiswa">Filter Beasiswa</label>
                    <select class="form-control mx-2" name="filter_beasiswa">
                        <option value="">Semua Beasiswa</option>
                        <?php
                        $sql_beasiswa = $connection->query("SELECT * FROM beasiswa");
                        while ($data_beasiswa = $sql_beasiswa->fetch_assoc()):
                            $kd_beasiswa_option = $data_beasiswa["kd_beasiswa"];
                            $nama_beasiswa_option = $data_beasiswa["nama_beasiswa"];
                        ?>
                            <option value="<?= $kd_beasiswa_option ?>" <?= ($kd_beasiswa_option == $selectedBeasiswa) ? 'selected' : '' ?>>
                                <?= $nama_beasiswa_option ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if (!$update): ?>
                        <button type="submit" name="Filter" class="btn btn-info">Filter</button>
                    <?php endif; ?>
                </div>
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Beasiswa</th>
                            <th>Kode Kriteria</th>
                            <th>Nama Kriteria</th>
                            <th>Sifat</th>
                            <th>Bobot</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php
                        $filter_beasiswa = isset($_POST['filter_beasiswa']) ? $_POST['filter_beasiswa'] : '';
                        $sql_kriteria = "SELECT a.nama AS kriteria, b.nama_beasiswa AS beasiswa, a.kd_kriteria, a.nama, a.sifat, a.bobot FROM kriteria a JOIN beasiswa b USING(kd_beasiswa)";
                        if ($filter_beasiswa) {
                            $sql_kriteria .= " WHERE a.kd_beasiswa = '$filter_beasiswa'";
                        }

                        $query = $connection->query($sql_kriteria);
                        ?>
                        <?php while($row = $query->fetch_assoc()): ?>
                            <tr>
                                <td><?=$no++?></td>
                                <td><?=$row['beasiswa']?></td>
                                <td><?=$row['kd_kriteria']?></td>
                                <td><?=$row['nama']?></td>
                                <td><?=$row['sifat']?></td>
                                <td><?=$row['bobot']?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?page=kriteria&action=update&key=<?=$row['kd_kriteria']?>" class="btn btn-warning btn-xs">Edit</a>
                                        <a href="?page=kriteria&action=delete&key=<?=$row['kd_kriteria']?>" class="btn btn-danger btn-xs">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
