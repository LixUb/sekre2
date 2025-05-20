<?php
// Include database connection
require_once 'config.php';

// Function to format date
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d F Y', $timestamp);
}

// Get requested date or use today
$requestDate = $_GET['date'] ?? date('Y-m-d');

// Get daily report data from database
function getDailyReport($conn, $date) {
    $report = [
        'total_diambil' => 0,
        'total_dikumpulkan' => 0, 
        'total_terlambat' => 0,
        'belum_dikumpulkan' => [],
        'detail' => []
    ];
    
    // Get daily statistics
    $sql_stats = "SELECT 
                    SUM(CASE WHEN action = 'ambil' THEN 1 ELSE 0 END) as total_diambil,
                    SUM(CASE WHEN action = 'kumpul' THEN 1 ELSE 0 END) as total_dikumpulkan,
                    SUM(CASE WHEN action = 'kumpul' AND status = 'terlambat' THEN 1 ELSE 0 END) as total_terlambat
                FROM laptop_transactions 
                WHERE DATE(transaction_time) = ?";
    
    $stmt_stats = mysqli_prepare($conn, $sql_stats);
    mysqli_stmt_bind_param($stmt_stats, "s", $date);
    mysqli_stmt_execute($stmt_stats);
    $result_stats = mysqli_stmt_get_result($stmt_stats);
    
    if ($row_stats = mysqli_fetch_assoc($result_stats)) {
        $report['total_diambil'] = $row_stats['total_diambil'] ?? 0;
        $report['total_dikumpulkan'] = $row_stats['total_dikumpulkan'] ?? 0;
        $report['total_terlambat'] = $row_stats['total_terlambat'] ?? 0;
    }
    
    // Get laptops not returned yet
    $sql_not_returned = "SELECT s.nis, s.name, s.class, 
                        DATE_FORMAT(ls.take_time, '%Y-%m-%d %H:%i:%s') as take_time
                        FROM laptop_status ls
                        JOIN students s ON ls.nis = s.nis
                        WHERE ls.status = 'diambil' 
                        AND DATE(ls.take_time) = ?";
    
    $stmt_not_returned = mysqli_prepare($conn, $sql_not_returned);
    mysqli_stmt_bind_param($stmt_not_returned, "s", $date);
    mysqli_stmt_execute($stmt_not_returned);
    $result_not_returned = mysqli_stmt_get_result($stmt_not_returned);
    
    while ($row = mysqli_fetch_assoc($result_not_returned)) {
        $report['belum_dikumpulkan'][] = $row;
    }
    
    // Get detailed activity
    $sql_details = "SELECT 
                    DATE_FORMAT(lt.transaction_time, '%Y-%m-%d %H:%i:%s') as time,
                    lt.action,
                    lt.status,
                    lt.nis,
                    s.name,
                    s.class
                  FROM laptop_transactions lt
                  JOIN students s ON lt.nis = s.nis
                  WHERE DATE(lt.transaction_time) = ?
                  ORDER BY lt.transaction_time DESC";
    
    $stmt_details = mysqli_prepare($conn, $sql_details);
    mysqli_stmt_bind_param($stmt_details, "s", $date);
    mysqli_stmt_execute($stmt_details);
    $result_details = mysqli_stmt_get_result($stmt_details);
    
    while ($row = mysqli_fetch_assoc($result_details)) {
        // Format action and status
        $action_display = $row['action'] == 'ambil' ? 'Ambil' : 'Kumpul';
        if ($row['action'] == 'kumpul' && $row['status'] == 'terlambat') {
            $action_display .= ' (Terlambat)';
        }
        
        $report['detail'][] = [
            'time' => $row['time'],
            'action' => $action_display,
            'nis' => $row['nis'],
            'name' => $row['name'],
            'class' => $row['class']
        ];
    }
    
    return $report;
}

$report = getDailyReport($conn, $requestDate);
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
    
    <?php mysqli_close($conn); ?>
</body>
</html>
