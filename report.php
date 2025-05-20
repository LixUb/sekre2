<?php
// File untuk melihat laporan pengambilan dan pengumpulan laptop
$isAdmin = true;


function getDailyReport($date = null) {
    if ($date === null) {
        $date = date('Y-m-d');
    }
    
    $report = [
        'total_diambil' => 0,
        'total_dikumpulkan' => 0, 
        'total_terlambat' => 0,
        'belum_dikumpulkan' => [],
        'detail' => []
    ];
    
    if (file_exists('laptop_log.txt')) {
        $lines = file('laptop_log.txt', FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $data = explode('|', $line);
            if (count($data) >= 5) {
                $logDate = substr($data[0], 0, 10);
                
                if ($logDate == $date) {
                    $report['detail'][] = [
                        'time' => $data[0],
                        'action' => $data[1],
                        'nis' => $data[2],
                        'name' => $data[3],
                        'class' => $data[4]
                    ];

                    if ($data[1] == 'Ambil') {
                        $report['total_diambil']++;
                    } elseif ($data[1] == 'Kumpul') {
                        $report['total_dikumpulkan']++;
                    } elseif ($data[1] == 'Kumpul (Terlambat)') {
                        $report['total_dikumpulkan']++;
                        $report['total_terlambat']++;
                    }
                }
            }
        }
    }
    
    if (file_exists('laptop_status.txt')) {
        $lines = file('laptop_status.txt', FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $data = explode('|', $line);
            if (count($data) >= 6 && $data[3] == 'Diambil') {
                $takeDate = substr($data[4], 0, 10);
                if ($takeDate == $date) {
                    $report['belum_dikumpulkan'][] = [
                        'nis' => $data[0],
                        'name' => $data[1],
                        'class' => $data[2],
                        'take_time' => $data[4]
                    ];
                }
            }
        }
    }
    
    return $report;
}

// Ambil tanggal yang diminta atau gunakan hari ini
$requestDate = $_GET['date'] ?? date('Y-m-d');
$report = getDailyReport($requestDate);

// Fungsi untuk format tanggal
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d F Y', $timestamp);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengambilan & Pengumpulan Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .report-container {
            padding: 20px;
        }
        
        .report-header {
            margin-bottom: 30px;
        }
        
        .report-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: #2a2a2a;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .date-selector {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container report-container">
        <div class="report-header">
            <h1 class="title">Laporan Pengambilan & Pengumpulan Laptop</h1>
            <p class="text-center">Tanggal: <?php echo formatDate($requestDate); ?></p>
            
            <div class="date-selector d-flex justify-content-center align-items-center">
                <form action="" method="GET" class="d-flex gap-3 align-items-center">
                    <label for="date">Pilih Tanggal:</label>
                    <input type="date" id="date" name="date" value="<?php echo $requestDate; ?>" class="form-control" style="width: auto;">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </form>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                <a href="index.php" class="btn btn-secondary">Kembali ke Sistem</a>
            </div>
        </div>
        
        <div class="report-stats">
            <div class="stat-card">
                <div class="stat-value"><?php echo $report['total_diambil']; ?></div>
                <div class="stat-label">Total Diambil</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $report['total_dikumpulkan']; ?></div>
                <div class="stat-label">Total Dikumpulkan</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $report['total_terlambat']; ?></div>
                <div class="stat-label">Terlambat Mengumpulkan</div>
            </div>
        </div>
        
        <!-- Laptop yang belum dikumpulkan -->
        <div class="laptop-status mt-4">
            <h3>Laptop Belum Dikumpulkan</h3>
            <?php if (count($report['belum_dikumpulkan']) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Waktu Ambil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['belum_dikumpulkan'] as $item): ?>
                                <tr>
                                    <td><?php echo $item['nis']; ?></td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['class']; ?></td>
                                    <td><?php echo $item['take_time']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">Tidak ada laptop yang belum dikumpulkan pada tanggal ini.</p>
            <?php endif; ?>
        </div>
        
        <!-- Detail Aktivitas -->
        <div class="laptop-status mt-4">
            <h3>Detail Aktivitas</h3>
            <?php if (count($report['detail']) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Aktivitas</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['detail'] as $item): ?>
                                <tr>
                                    <td><?php echo $item['time']; ?></td>
                                    <td><?php echo $item['action']; ?></td>
                                    <td><?php echo $item['nis']; ?></td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['class']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">Tidak ada aktivitas pada tanggal ini.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>