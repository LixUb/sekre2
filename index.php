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
                        if (file_exists('laptop_status.txt')) {
                            $lines = file('laptop_status.txt', FILE_IGNORE_NEW_LINES);
                            foreach ($lines as $line) {
                                $data = explode('|', $line);
                                if (count($data) >= 6) {
                                    echo "<tr>";
                                    echo "<td>{$data[0]}</td>"; // NIS
                                    echo "<td>{$data[1]}</td>"; // Nama
                                    echo "<td>{$data[2]}</td>"; // Kelas
                                    echo "<td>{$data[3]}</td>"; // Status
                                    echo "<td>{$data[4]}</td>"; // Waktu Ambil
                                    echo "<td>{$data[5]}</td>"; // Waktu Kumpul
                                    echo "</tr>";
                                }
                            }
                        }
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