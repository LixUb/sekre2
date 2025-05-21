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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengambilan & Pengumpulan Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --dark-bg: #232946;
            --card-bg: #293462;
            --text-light: #fffffe;
            --text-muted: #b8c1ec;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-light);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            padding: 2rem 1.5rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            border-radius: 16px;
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDUwMCA1MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPGRlZnM+CiAgICA8Y2xpcFBhdGggaWQ9ImNpcmNsZUNsaXAiPgogICAgICA8Y2lyY2xlIGN4PSIyNTAiIGN5PSIyNTAiIHI9IjIwMCIgLz4KICAgIDwvY2xpcFBhdGg+CiAgPC9kZWZzPgogIDxjaXJjbGUgY3g9IjI1MCIgY3k9IjI1MCIgcj0iMTUwIiBmaWxsPSJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuMDMpIiAvPgogIDxjaXJjbGUgY3g9IjI1MCIgY3k9IjI1MCIgcj0iMTAwIiBmaWxsPSJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuMDMpIiAvPgogIDxjaXJjbGUgY3g9IjI1MCIgY3k9IjI1MCIgcj0iNTAiIGZpbGw9InJnYmEoMjU1LCAyNTUsIDI1NSwgMC4wMykiIC8+Cjwvc3ZnPg==') no-repeat center right;
            opacity: 0.1;
            z-index: 0;
        }
        
        .page-header h1 {
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .page-header p {
            color: var(--text-light);
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }
        
        .date-selector {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.2rem;
            margin-top: 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .date-selector input {
            background-color: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }
        
        .date-selector input:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .date-selector label {
            color: var(--text-light);
            margin-right: 10px;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--success-color);
            border: none;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #3db8de;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(76, 201, 240, 0.3);
        }
        
        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--text-light);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        
        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card .icon {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            display: inline-block;
            padding: 1rem;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .stat-card:nth-child(1) .icon {
            color: #4cc9f0;
        }
        
        .stat-card:nth-child(2) .icon {
            color: #4895ef;
        }
        
        .stat-card:nth-child(3) .icon {
            color: #f72585;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
            color: white;
        }
        
        .stat-label {
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 500;
        }
        
        .content-card {
            background-color: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        
        .content-card h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
        }
        
        .content-card h3 i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: rgba(0, 0, 0, 0.15);
            color: var(--text-light);
            font-weight: 600;
            border-bottom: none;
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }
        
        .table tbody td {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }
        
        .table-dark {
            background-color: transparent;
            color: var(--text-light);
        }
        
        .table-dark.table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: rgba(255, 255, 255, 0.03);
            color: var(--text-light);
        }
        
        .text-center {
            color: var(--text-muted);
            font-style: italic;
            padding: 2rem;
        }
        
        .badge {
            padding: 0.4em 0.8em;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 6px;
        }
        
        .badge-warning {
            background-color: var(--warning-color);
            color: #2b2c34;
        }
        
        .footer {
            margin-top: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 767px) {
            .page-header {
                padding: 1.5rem;
                text-align: center;
            }
            
            .date-selector {
                flex-direction: column;
                align-items: stretch !important;
                gap: 1rem;
            }
            
            .date-selector form {
                flex-direction: column;
                align-items: stretch !important;
                gap: 1rem;
            }
            
            .stat-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header class="page-header">
            <h1>Laporan Pengambilan & Pengumpulan Laptop</h1>
            <p>Statistik dan aktivitas untuk tanggal: <?php echo formatDate($requestDate); ?></p>
            
            <div class="date-selector d-flex justify-content-between align-items-center">
                <form action="" method="GET" class="d-flex gap-3 align-items-center flex-grow-1">
                    <div class="d-flex align-items-center flex-grow-1">
                        <label for="date"><i class="fas fa-calendar-alt me-2"></i> Pilih Tanggal:</label>
                        <input type="date" id="date" name="date" value="<?php echo $requestDate; ?>" class="form-control ms-2">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i> Tampilkan
                    </button>
                </form>
                
                <a href="index.php" class="btn btn-secondary ms-3">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </header>
        
        <!-- Stats Cards -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="stat-value"><?php echo $report['total_diambil']; ?></div>
                <div class="stat-label">Total Diambil</div>
            </div>
            <div class="stat-card">
                <div class="icon">
                    <i class="fas fa-laptop-house"></i>
                </div>
                <div class="stat-value"><?php echo $report['total_dikumpulkan']; ?></div>
                <div class="stat-label">Total Dikumpulkan</div>
            </div>
            <div class="stat-card">
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $report['total_terlambat']; ?></div>
                <div class="stat-label">Terlambat Mengumpulkan</div>
            </div>
        </div>
        
        <!-- Laptop yang belum dikumpulkan -->
        <div class="content-card">
            <h3><i class="fas fa-exclamation-triangle"></i> Laptop Belum Dikumpulkan</h3>
            <?php if (count($report['belum_dikumpulkan']) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Waktu Ambil</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['belum_dikumpulkan'] as $item): ?>
                                <tr>
                                    <td><strong><?php echo $item['nis']; ?></strong></td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['class']; ?></td>
                                    <td><?php echo $item['take_time']; ?></td>
                                    <td><span class="badge badge-warning">Belum Dikumpulkan</span></td>
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
        <div class="content-card">
            <h3><i class="fas fa-clipboard-list"></i> Detail Aktivitas</h3>
            <?php if (count($report['detail']) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Aktivitas</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['detail'] as $item): ?>
                                <tr>
                                    <td><?php echo $item['time']; ?></td>
                                    <td>
                                        <?php if (strpos($item['action'], 'Ambil') !== false): ?>
                                            <span class="badge bg-primary"><i class="fas fa-arrow-right me-1"></i> <?php echo $item['action']; ?></span>
                                        <?php elseif (strpos($item['action'], 'Terlambat') !== false): ?>
                                            <span class="badge bg-danger"><i class="fas fa-arrow-left me-1"></i> <?php echo $item['action']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="fas fa-arrow-left me-1"></i> <?php echo $item['action']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo $item['nis']; ?></strong></td>
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
        
        <footer class="footer">
            <p>© <?php echo date('Y'); ?> Sistem Peminjaman Laptop Sekolah</p>
        </footer>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php mysqli_close($conn); ?>
</body>
</html>