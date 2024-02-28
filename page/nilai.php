<?php
$update = (isset($_GET['action']) && $_GET['action'] == 'update') ? true : false;

if ($update) {
    $key = $_GET['key'];
    $key = $connection->real_escape_string($key);
    $sql = $connection->query("SELECT * FROM nilai WHERE kd_nilai='$key'");

    if ($sql->num_rows > 0) {
        $row = $sql->fetch_assoc();
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["save"])) {
    $validasi = false;
    $err = false;

    // Periksa apakah ada nilai yang dipilih
    if (empty($_POST["nilai"])) {
        echo alert("Pilih setidaknya satu nilai!", "?page=nilai");
        exit; // Hentikan eksekusi jika tidak ada nilai yang dipilih
    }

    if ($update) {
        $sql = $connection->prepare("UPDATE nilai SET kd_beasiswa=?, kd_kriteria=?, nisn=?, nilai=? WHERE kd_nilai=?");
        $sql->bind_param("sssss", $_POST['kd_beasiswa'], $kd_kriteria, $_POST['nisn'], $nilai, $_GET['key']);
        $kd_beasiswa = $_POST['kd_beasiswa'];
        $nisn = $_POST['nisn'];

        foreach ($_POST["nilai"] as $kd_kriteria => $nilai) {
            // Periksa apakah nilai sudah ada
            $q = $connection->prepare("SELECT kd_nilai FROM nilai WHERE kd_beasiswa=? AND kd_kriteria=? AND nisn=? AND kd_nilai != ?");
            $q->bind_param("ssss", $kd_beasiswa, $kd_kriteria, $nisn, $_GET['key']);
            $q->execute();
            $result = $q->get_result();
    
            if ($result->num_rows) {
                continue; // Lanjutkan proses update jika nilai sudah ada (kecuali nilai yang sedang diedit)
            }
    
            // Tambahkan nilai ke dalam database menggunakan prepared statement
            $sql->execute();
        }

        if (!$err) {
            $sql->execute();
            echo alert("Berhasil!", "?page=nilai");
        } else {
            echo alert("Gagal!", "?page=nilai");
        }
        
    } else {
        $query = "INSERT INTO nilai (kd_beasiswa, kd_kriteria, nisn, nilai) VALUES (?, ?, ?, ?)";

        foreach ($_POST["nilai"] as $kd_kriteria => $nilai) {
            // Periksa apakah nilai sudah ada
            $q = $connection->prepare("SELECT kd_nilai FROM nilai WHERE kd_beasiswa=? AND kd_kriteria=? AND nisn=?");
            $q->bind_param("sss", $_POST['kd_beasiswa'], $kd_kriteria, $_POST['nisn']);
            $q->execute();
            $result = $q->get_result();

            if ($result->num_rows) {
                echo alert("Nilai untuk " . $_POST["nisn"] . " sudah ada!", "?page=nilai");
                $err = true;
                break; // Hentikan loop jika nilai sudah ada
            }

            // Tambahkan nilai ke database menggunakan prepared statement
            $insertStatement = $connection->prepare($query);
            $insertStatement->bind_param("ssss", $_POST['kd_beasiswa'], $kd_kriteria, $_POST['nisn'], $nilai);
            $insertStatement->execute();
        }

        if (!$err) {
            echo alert("Berhasil!", "?page=nilai");
        } else {
            echo alert("Gagal!", "?page=nilai");
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $connection->query("DELETE FROM nilai WHERE kd_nilai='$_GET[key]'");
    echo alert("Berhasil!", "?page=nilai");
}
?>

<!-- Form -->
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
            <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
            <div class="panel-body">
                <form action="<?=$_SERVER["REQUEST_URI"]?>" method="post" id="nilaiForm">

                    <!-- Input NISN -->
                    <div class="form-group">
                        <label for="nisn">Siswa</label>
                        <?php if ($_POST || $update): ?>
                            <input type="text" name="nisn" value="<?= ($update) ? $row['nisn'] : $_POST["nisn"] ?>" class="form-control" readonly="on">
                        <?php else: ?>
                            <select class="form-control" name="nisn">
                                <option>---</option>
                                <?php
                                $sql = $connection->query("SELECT * FROM siswa");
                                while ($data = $sql->fetch_assoc()):
                                    $nisn = $data["nisn"];
                                    $nama = $data["nama"];
                                    $tahun = $data["tahun_mengajukan"];
                                    $periode = $data["periode"];
                                ?>
                                    <option value="<?=$nisn?>" <?= (!$update) ? "" : (($row["nisn"] != $nisn) ? "" : 'selected="selected"') ?>>
                                        <?=$nisn?> | <?=$nama?> | Tahun: <?=$tahun?> | Periode: <?=$periode?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <!-- Input Beasiswa -->
                    <div class="form-group">
                        <label for="kd_beasiswa">Beasiswa</label>
                        <?php if ($_POST || $update): ?>
                            <?php $q = $connection->query("SELECT nama_beasiswa FROM beasiswa WHERE kd_beasiswa=" . (($update) ? $row['kd_beasiswa'] : $_POST['kd_beasiswa'])); ?>
                            <input type="text" value="<?=$q->fetch_assoc()["nama_beasiswa"]?>" class="form-control" readonly="on">
                            <input type="hidden" name="kd_beasiswa" value="<?= ($update) ? $row['kd_beasiswa'] : $_POST["kd_beasiswa"] ?>">
                        <?php else: ?>
                            <select class="form-control" name="kd_beasiswa" id="beasiswa">
                                <option>---</option>
                                <?php $sql = $connection->query("SELECT * FROM beasiswa"); while ($data = $sql->fetch_assoc()): ?>
                                    <option value="<?=$data["kd_beasiswa"]?>"<?= (!$update) ? "" : (($row["kd_beasiswa"] != $data["kd_beasiswa"]) ? "" : 'selected="selected"') ?>><?=$data["nama_beasiswa"]?></option>
                                <?php endwhile; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    
                   <!-- Input Nilai -->
                    <?php if ($_POST): ?>
                        <?php
                        // Ambil kd_beasiswa terkait
                        $kd_beasiswa = ($update) ? $row['kd_beasiswa'] : $_POST['kd_beasiswa'];
                        
                        // Ambil kriteria yang terkait dengan kd_beasiswa
                        $sql_kriteria = $connection->query("SELECT * FROM kriteria WHERE kd_beasiswa='$kd_beasiswa'");
                        ?>

                        <?php while ($r = $sql_kriteria->fetch_assoc()): ?>
                            <label for="nilai_<?= $r["kd_kriteria"] ?>"> <?= $r["nama"] ?></label>
                            <div class="form-group">
                                <select class="form-control" name="nilai[<?= $r["kd_kriteria"] ?>]" id="nilai_<?= $r["kd_kriteria"] ?>">
                                    <option>---</option>
                                    <?php
                                    for ($i = 1; $i <= 4; $i++) {
                                        echo '<option value="' . $i . '" ' . ((!$update) ? '' : (($row["nilai"][$r["kd_kriteria"]] != $i) ? '' : 'selected="selected"')) . '>' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                                <p>* Masukkan bobot</p>
                            </div>
                        <?php endwhile; ?>
                        <input type="hidden" name="save" value="true">
                    <?php endif; ?>


                    <!-- Input Nilai -->
                    <?php if ($update): ?>
                        <?php
                        // Ambil kd_kriteria terkait
                        $kd_kriteria_edit = ($update) ? $row['kd_kriteria'] : $_POST['kd_kriteria'];
                        
                        // Ambil kd_beasiswa terkait
                        $kd_beasiswa = ($update) ? $row['kd_beasiswa'] : $_POST['kd_beasiswa'];
                        
                        // Ambil kriteria yang terkait dengan kd_beasiswa
                        $sql_kriteria = $connection->query("SELECT * FROM kriteria WHERE kd_beasiswa='$kd_beasiswa' AND kd_kriteria='$kd_kriteria_edit'");
                        ?>

                        <?php while ($r = $sql_kriteria->fetch_assoc()): ?>
                            <label for="nilai_<?= $r["kd_kriteria"] ?>"> <?= $r["nama"] ?></label>
                            <div class="form-group">
                                <select class="form-control" name="nilai[<?= $r["kd_kriteria"] ?>]" id="nilai_<?= $r["kd_kriteria"] ?>">
                                    <option>---</option>
                                    <?php
                                    for ($i = 1; $i <= 4; $i++) {
                                        echo '<option value="' . $i . '" ' . ((!$update) ? '' : (($row["nilai"][$r["kd_kriteria"]] != $i) ? '' : 'selected="selected"')) . '>' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                                <p>* Masukkan bobot</p>
                            </div>
                        <?php endwhile; ?>
                        <input type="hidden" name="save" value="true">
                    <?php endif; ?>
                    
                    <!-- Tombol Simpan -->
                    <button type="submit" id="simpan" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block"><?=($_POST) ? "Simpan" : "Tampilkan"?></button>
                    <?php if ($update): ?>
                        <a href="?page=nilai" class="btn btn-info btn-block">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>


<!-- Tabel Daftar -->

    <div class="col-md-8">
        <div class="panel panel-info">
            <div class="panel-heading"><h3 class="text-center">DAFTAR</h3></div>
            <div class="panel-body">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Beasiswa</th>
                            <th>Tahun</th>
                            <th>Periode</th>
                            <th>Kriteria</th>
                            <th>Nilai</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($query = $connection->query("SELECT a.kd_nilai, nama_beasiswa, b.nama AS nama_kriteria, d.nisn, d.nama AS nama_siswa, a.nilai, d.tahun_mengajukan, d.periode 
                            FROM nilai a 
                            JOIN kriteria b ON a.kd_kriteria=b.kd_kriteria 
                            JOIN beasiswa c ON a.kd_beasiswa=c.kd_beasiswa 
                            JOIN siswa d ON d.nisn=a.nisn
                            ORDER BY a.kd_nilai")):

                            while($row = $query->fetch_assoc()): ?>
                                <tr>
                                    <td><?=$no++?></td>
                                    <td><?=$row['nisn']?></td>
                                    <td><?=$row['nama_siswa']?></td>
                                    <td><?=$row['nama_beasiswa']?></td>
                                    <td><?=$row['tahun_mengajukan']?></td>
                                    <td><?=$row['periode']?></td>
                                    <td><?=$row['nama_kriteria']?></td>
                                    <td><?=$row['nilai']?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="?page=nilai&action=update&key=<?=$row['kd_nilai']?>" class="btn btn-warning btn-xs">Edit</a>
                                            <a href="?page=nilai&action=delete&key=<?=$row['kd_nilai']?>" class="btn btn-danger btn-xs">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script Chained -->
<script type="text/javascript">
    <?php $q = $connection->query("SELECT * FROM kriteria WHERE kd_beasiswa=" . (($update) ? $row['kd_beasiswa'] : $_POST['kd_beasiswa'])); ?>
    <?php while ($r = $q->fetch_assoc()): ?>
        $("#nilai_<?= $r["kd_kriteria"] ?>").chained("#kriteria");
    <?php endwhile; ?>
</script>