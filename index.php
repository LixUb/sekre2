<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kumpulin laptop kalian woyy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1200px;
            padding: 2rem 1.5rem;
            flex: 1;
        }

        .header-container {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
            position: relative;
            overflow: hidden;
        }
        
        .header-container::after {
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

        .logo {
            height: 70px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
            z-index: 1;
            position: relative;
        }
        
        .logo:hover {
            transform: scale(1.05);
        }

        .title {
            font-weight: 700;
            color: var(--text-light);
            margin: 0;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .scan-area {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .scan-area:hover {
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }
        
        .scan-area i {
            font-size: 2rem;
            margin-bottom: 1rem;
            display: block;
            color: var(--success-color);
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
        }

        .action-buttons .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline-warning {
            color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-outline-warning:hover, .btn-outline-warning.active {
            background-color: var(--warning-color);
            color: #000;
            border-color: var(--warning-color);
            transform: translateY(-3px);
        }

        .btn-outline-primary {
            color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-outline-primary:hover, .btn-outline-primary.active {
            background-color: var(--success-color);
            color: #000;
            border-color: var(--success-color);
            transform: translateY(-3px);
        }

        .btn-outline-info {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-info:hover {
            background-color: var(--primary-color);
            color: var(--text-light);
            border-color: var(--primary-color);
            transform: translateY(-3px);
        }

        .action-buttons .active {
            box-shadow: 0 0 15px rgba(255, 165, 0, 0.5);
        }

        .status-display {
            background-color: var(--card-bg);
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .status-display p {
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }
        
        .status-display p i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .status-display p strong {
            margin-left: 0.5rem;
            font-weight: 600;
        }

        #barcode-input {
            width: 100%;
            padding: 1.2rem;
            font-size: 1.2rem;
            border-radius: 12px;
            border: none;
            background-color: var(--card-bg);
            color: var(--text-light);
            margin-top: 1rem;
            margin-bottom: 1rem;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        #barcode-input:focus {
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.3);
            outline: none;
        }

        #barcode-input::placeholder {
            color: var(--text-muted);
        }

        .result {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 12px;
            background-color: var(--card-bg);
            color: var(--text-light);
            font-weight: 500;
            text-align: center;
            border-left: 5px solid var(--success-color);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .laptop-status {
            background-color: var(--card-bg);
            padding: 1.5rem;
            border-radius: 16px;
            margin-top: 2rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .laptop-status h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
        }

        .laptop-status h3 i {
            margin-right: 10px;
            color: var(--warning-color);
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
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

        .status-badge {
            padding: 0.4em 0.8em;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 6px;
        }

        .status-diambil {
            background-color: var(--warning-color);
            color: #000;
        }

        .status-dikumpulkan {
            background-color: var(--success-color);
            color: #000;
        }

        .status-terlambat {
            background-color: var(--danger-color);
            color: var(--text-light);
        }

        footer {
            padding: 1.5rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 1.5rem;
            }
            
            .logo {
                height: 50px;
            }
            
            .title {
                font-size: 1.6rem;
            }
            
            .scan-area {
                padding: 1.5rem;
            }
            
            .status-display {
                flex-direction: column;
                gap: 0.8rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header-container">
            <div class="d-flex align-items-center justify-content-between">
                <img src="Kemenag.png" alt="Logo Kemenag" class="logo">
                <h1 class="title flex-grow-1 text-center">Udah Cape Sama Madrasah Yaa</h1>
                <img src="IC.png" alt="Logo IC" class="logo">
            </div>
        </header>
        <div class="scan-area">
            <i class="fas fa-laptop-code"></i>
            <div>Scan barcode siswa untuk memproses pengambilan atau pengumpulan laptop</div>
        </div>

        <?php if (isset($_GET['message'])): ?>
            <div class="result">
                <i class="fas fa-info-circle me-2"></i>
                <?php echo urldecode($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="?action=ambil" class="btn btn-outline-warning <?php echo (!isset($_GET['action']) || $_GET['action'] == 'ambil') ? 'active' : ''; ?>">
                <i class="fas fa-laptop"></i> Mode Pengambilan
            </a>
            <a href="?action=kumpul" class="btn btn-outline-primary <?php echo (isset($_GET['action']) && $_GET['action'] == 'kumpul') ? 'active' : ''; ?>">
                <i class="fas fa-laptop-house"></i> Mode Pengumpulan
            </a>
            <a href="report.php" class="btn btn-outline-info">
                <i class="fas fa-chart-bar"></i> Lihat Laporan
            </a>
        </div>

        <div class="status-display">
            <p>
                <i class="fas <?php echo isset($_GET['action']) && $_GET['action'] == 'kumpul' ? 'fa-laptop-house' : 'fa-laptop'; ?>"></i>
                Mode Aktif: <strong><?php echo isset($_GET['action']) && $_GET['action'] == 'kumpul' ? 'Pengumpulan Laptop' : 'Pengambilan Laptop'; ?></strong>
            </p>
            <p>
                <i class="fas fa-clock"></i>
                Waktu Sekarang: <strong id="current-time"></strong>
            </p>
        </div>

        <form id="barcode-form" action="proses.php" method="POST">
            <input type="text" name="barcode" id="barcode-input" autofocus autocomplete="off" placeholder="Scan atau ketik NISN siswa di sini...">
            <input type="hidden" name="action" value="<?php echo isset($_GET['action']) && $_GET['action'] == 'kumpul' ? 'kumpul' : 'ambil'; ?>">
        </form>

        <div class="laptop-status">
            <h3><i class="fas fa-clipboard-list"></i> Status Laptop Terkini</h3>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>NISN</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <footer>
        <p>&copy; <?php echo date("Y"); ?> MAN INSAN CENDEKIA KOTA BATAM | @rayyhfz_ </p>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        function updateTime() {
            const now = new Date();
            const options = { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false
            };
            document.getElementById("current-time").textContent = now.toLocaleTimeString("id-ID", options);
        }
        
        updateTime();
        
        setInterval(updateTime, 1000);

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('barcode-input').focus();
        });
    </script>
</body>
</html>