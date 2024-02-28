<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tahun = $_POST["tahun"];

    // Query untuk mengambil NISN, Nama, Nama Beasiswa, dan nilai maksimal (total bobot)
    $sql = "SELECT siswa.nisn, siswa.nama, beasiswa.nama_beasiswa, SUM(kriteria.bobot) AS total_bobot
            FROM siswa
            JOIN beasiswa ON siswa.kd_beasiswa = beasiswa.kd_beasiswa
            JOIN kriteria ON beasiswa.kd_beasiswa = kriteria.kd_beasiswa
            WHERE siswa.tahun_mengajukan = '$tahun'
            GROUP BY siswa.nisn, beasiswa.nama_beasiswa";

    // Eksekusi query
    $query = $connection->query($sql);
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="text-center">Laporan Nilai Maksimal Siswa Berdasarkan Beasiswa</h3>
            </div>
            <div class="panel-body">
                <form class="form-inline" action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
                    <label for="tahun">Tahun :</label>
                    <select class="form-control" name="tahun">
                        <option>---</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </form>
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <hr>
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Nama Beasiswa</th>
                            <th>Nilai Maksimal Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $query->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["nisn"] . "</td>";
                            echo "<td>" . $row["nama"] . "</td>";
                            echo "<td>" . $row["nama_beasiswa"] . "</td>";
                            echo "<td>" . $row["total_bobot"] . "</td>";
                            echo "</tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
