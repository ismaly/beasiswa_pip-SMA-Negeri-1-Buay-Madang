<?php
// Inisialisasi variabel tahun dan periode yang digunakan untuk filter
$selectedYear = isset($_POST['tahun_mengajukan']) ? $_POST['tahun_mengajukan'] : "";
$selectedPeriode = isset($_POST['periode']) ? $_POST['periode'] : "";

// Query database dengan filter tahun dan periode yang dipilih
$query = "SELECT s.kode_siswa, s.nisn, s.nama, s.tahun_mengajukan, s.periode, 
          MAX(CASE WHEN n.kd_kriteria = 'C1' THEN n.nilai END) AS nilai_C1,
          MAX(CASE WHEN n.kd_kriteria = 'C2' THEN n.nilai END) AS nilai_C2,
          MAX(CASE WHEN n.kd_kriteria = 'C3' THEN n.nilai END) AS nilai_C3,
          MAX(CASE WHEN n.kd_kriteria = 'C4' THEN n.nilai END) AS nilai_C4,
          MAX(CASE WHEN n.kd_kriteria = 'C5' THEN n.nilai END) AS nilai_C5
          FROM siswa s
          LEFT JOIN nilai n ON s.nisn = n.nisn";
if (!empty($selectedYear) && !empty($selectedPeriode)) {
    $query .= " WHERE s.tahun_mengajukan = '$selectedYear' AND s.periode = '$selectedPeriode'";
} elseif (!empty($selectedYear)) {
    $query .= " WHERE s.tahun_mengajukan = '$selectedYear'";
} elseif (!empty($selectedPeriode)) {
    $query .= " WHERE s.periode = '$selectedPeriode'";
}
$query .= " GROUP BY s.kode_siswa, s.nisn, s.nama, s.tahun_mengajukan, s.periode";

$result = $connection->query($query);

// Periksa apakah query berhasil dieksekusi
if (!$result) {
    echo "Query error: " . $connection->error;
    exit;
}

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="text-center">Tabel Matriks</h3>
            </div>
            <div class="panel-body">
                <input type="hidden" name="selectedYear" id="selectedYear" value="<?= htmlspecialchars($selectedYear) ?>">
                <input type="hidden" name="selectedPeriode" id="selectedPeriode" value="<?= htmlspecialchars($selectedPeriode) ?>">

                <form class="form-inline" action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
                    <label for="tahun_mengajukan">Tahun :</label>
                    <select class="form-control" id="tahun" name="tahun_mengajukan">
                        <option value="">Pilih Tahun</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                    </select>
                
                    <label for="periode" id="label-periode">Periode :</label>
                    <select class="form-control" id="periode" name="periode">
                        <option>---</option>
                        <option value="Tahap1">Tahap 1</option>
                        <option value="Tahap2">Tahap 2</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="generate_pdf.php" class="btn btn-success">Unduh PDF</a>

                </form>
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Siswa</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>C1</th>
                            <th>C2</th>
                            <th>C3</th>
                            <th>C4</th>
                            <th>C5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . $row['kode_siswa'] . "</td>";
                            echo "<td>" . $row['nisn'] . "</td>";
                            echo "<td>" . $row['nama'] . "</td>";
                            echo "<td>" . $row['nilai_C1'] . "</td>";
                            echo "<td>" . $row['nilai_C2'] . "</td>";
                            echo "<td>" . $row['nilai_C3'] . "</td>";
                            echo "<td>" . $row['nilai_C4'] . "</td>";
                            echo "<td>" . $row['nilai_C5'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="text-center">Tabel Normalisasi</h3>
            </div>
            <div class="panel-body">
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Siswa</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>C1</th>
                            <th>C2</th>
                            <th>C3</th>
                            <th>C4</th>
                            <th>C5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reset nomor urut
                        $no = 1;
                        // Kembalikan kursor hasil query ke awal
                        $result->data_seek(0);
                        // Loop through hasil query
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . $row['kode_siswa'] . "</td>";
                            echo "<td>" . $row['nisn'] . "</td>";
                            echo "<td>" . $row['nama'] . "</td>";
                            // Hitung normalisasi C1 - C5
                            $normalisasi_C1 = 3 / $row['nilai_C1']; // Misalnya 4 adalah nilai maksimum
                            $normalisasi_C2 = $row['nilai_C2'] / 4; // Misalnya 4 adalah nilai maksimum
                            $normalisasi_C3 = $row['nilai_C3'] / 4; // Misalnya 4 adalah nilai maksimum
                            $normalisasi_C4 = 3 / $row['nilai_C4']; // Misalnya 3 adalah nilai minimum
                            $normalisasi_C5 = $row['nilai_C5'] / 4; // Misalnya 4 adalah nilai maksimum
                            // Tampilkan normalisasi C1 - C5
                            echo "<td>" . $normalisasi_C1 . "</td>";
                            echo "<td>" . $normalisasi_C2 . "</td>";
                            echo "<td>" . $normalisasi_C3 . "</td>";
                            echo "<td>" . $normalisasi_C4 . "</td>";
                            echo "<td>" . $normalisasi_C5 . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="text-center">Tabel Perangkingan</h3>
            </div>
            <div class="panel-body">
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Siswa</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>C1</th>
                            <th>C2</th>
                            <th>C3</th>
                            <th>C4</th>
                            <th>C5</th>
                            <th>Perangkingan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Urutkan hasil perangkingan dari yang tertinggi ke yang terendah
                        $result->data_seek(0); // Kembalikan kursor hasil query ke awal
                        $perangkingan_arr = []; // Array untuk menyimpan hasil perangkingan
                        while ($row = $result->fetch_assoc()) {
                            // Hitung normalisasi C1 - C5
                            $normalisasi_C1 = 3 / $row['nilai_C1']; // Misalnya 4 adalah nilai maksimum
                            $normalisasi_C2 = $row['nilai_C2'] / 4; // Misalnya 4 adalah nilai maksimum
                            $normalisasi_C3 = $row['nilai_C3'] / 4; // Misalnya 4 adalah nilai maksimum
                            $normalisasi_C4 = 3 / $row['nilai_C4']; // Misalnya 3 adalah nilai minimum
                            $normalisasi_C5 = $row['nilai_C5'] / 4; // Misalnya 4 adalah nilai maksimum

                            // Hitung perangkingan untuk setiap kriteria
                            $perangkingan_C1 = $normalisasi_C1 * 30;
                            $perangkingan_C2 = $normalisasi_C2 * 15;
                            $perangkingan_C3 = $normalisasi_C3 * 10;
                            $perangkingan_C4 = $normalisasi_C4 * 25;
                            $perangkingan_C5 = $normalisasi_C5 * 20;

                            // Simpan hasil perangkingan ke dalam array bersama dengan data lainnya
                            $perangkingan_arr[] = array(
                                'kode_siswa' => $row['kode_siswa'],
                                'nisn' => $row['nisn'],
                                'nama' => $row['nama'],
                                'perangkingan_C1' => $perangkingan_C1,
                                'perangkingan_C2' => $perangkingan_C2,
                                'perangkingan_C3' => $perangkingan_C3,
                                'perangkingan_C4' => $perangkingan_C4,
                                'perangkingan_C5' => $perangkingan_C5,
                                'perangkingan' => $perangkingan_C1 + $perangkingan_C2 + $perangkingan_C3 + $perangkingan_C4 + $perangkingan_C5
                            );
                        }
                        // Urutkan array perangkingan berdasarkan nilai perangkingan secara descending
                        usort($perangkingan_arr, function ($a, $b) {
                            return $b['perangkingan'] <=> $a['perangkingan'];
                        });
                        // Tampilkan hasil perangkingan dalam tabel
                        $no = 1;
                        foreach ($perangkingan_arr as $siswa) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . $siswa['kode_siswa'] . "</td>";
                            echo "<td>" . $siswa['nisn'] . "</td>";
                            echo "<td>" . $siswa['nama'] . "</td>";
                            echo "<td>" . $siswa['perangkingan_C1'] . "</td>";
                            echo "<td>" . $siswa['perangkingan_C2'] . "</td>";
                            echo "<td>" . $siswa['perangkingan_C3'] . "</td>";
                            echo "<td>" . $siswa['perangkingan_C4'] . "</td>";
                            echo "<td>" . $siswa['perangkingan_C5'] . "</td>";
                            echo "<td>" . $siswa['perangkingan'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
