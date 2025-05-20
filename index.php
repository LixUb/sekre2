<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistem Pengambilan & Pengumpulan Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Sistem Pengambilan & Pengumpulan Laptop</h1>
        
        <div class="scan-area">
            Scan barcode siswa...
        </div>

        <?php if (isset($_GET['message'])): ?>
            <div class="result">
                <?php echo urldecode($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="?action=ambil" class="btn btn-success <?php echo (!isset($_GET['action']) || $_GET['action'] == 'ambil') ? 'active' : ''; ?>">Mode Pengambilan</a>
            <a href="?action=kumpul" class="btn btn-primary <?php echo (isset($_GET['action']) && $_GET['action'] == 'kumpul') ? 'active' : ''; ?>">Mode Pengumpulan</a>
            <a href="report.php" class="btn btn-info">Lihat Laporan</a>
        </div>

        <div class="status-display">
            <p>Mode: <strong><?php echo isset($_GET['action']) && $_GET['action'] == 'kumpul' ? 'Pengumpulan Laptop' : 'Pengambilan Laptop'; ?></strong></p>
            <p>Waktu Sekarang: <strong id="current-time"></strong></p>
        </div>

        <form id="barcode-form" action="proses.php" method="POST">
            <input type="text" name="barcode" id="barcode-input" autofocus autocomplete="off">
            <input type="hidden" name="action" value="<?php echo isset($_GET['action']) && $_GET['action'] == 'kumpul' ? 'kumpul' : 'ambil'; ?>">
        </form>

        <div class="laptop-status mt-4">
            <h3>Status Laptop Terkini</h3>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Waktu Ambil</th>
                            <th>Waktu Kumpul</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Include database connection
                        require_once 'config.php';

                        // Query to get the latest laptop status for each student
                        $sql = "SELECT s.nis, s.name, s.class, ls.status, 
                                DATE_FORMAT(ls.take_time, '%Y-%m-%d %H:%i:%s') as take_time, 
                                DATE_FORMAT(ls.return_time, '%Y-%m-%d %H:%i:%s') as return_time 
                                FROM students s
                                LEFT JOIN laptop_status ls ON s.nis = ls.nis
                                ORDER BY 
                                    CASE ls.status 
                                        WHEN 'diambil' THEN 1 
                                        WHEN 'dikumpul_terlambat' THEN 2
                                        WHEN 'dikumpul' THEN 3
                                        ELSE 4
                                    END";

                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>{$row['nis']}</td>";
                                echo "<td>{$row['name']}</td>";
                                echo "<td>{$row['class']}</td>";
                                
                                // Format status for display
                                $status_display = '';
                                if ($row['status'] == 'diambil') {
                                    $status_display = 'Diambil';
                                } else if ($row['status'] == 'dikumpul') {
                                    $status_display = 'Terkumpul';
                                } else if ($row['status'] == 'dikumpul_terlambat') {
                                    $status_display = 'Terkumpul (Terlambat)';
                                } else {
                                    $status_display = '-';
                                }
                                
                                echo "<td>{$status_display}</td>";
                                echo "<td>" . ($row['take_time'] ?? '-') . "</td>";
                                echo "<td>" . ($row['return_time'] ?? '-') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Tidak ada data</td></tr>";
                        }
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const input = document.getElementById("barcode-input");
        window.onload = () => input.focus();
        input.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                document.getElementById("barcode-form").submit();
            }
        });

        function updateCurrentTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('current-time').textContent = now.toLocaleDateString('id-ID', options);
        }

        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
    </script>
</body>
</html>