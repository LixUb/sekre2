<?php
$students = [
    "0079787882" => ["name" => "Rayhan Nulhafiz", "class" => "XI D"],
    

];

$barcode = trim($_POST['barcode'] ?? '');
$action = $_POST['action'] ?? 'ambil';
$time = date("Y-m-d H:i:s");
$currentHour = (int)date("H");
$currentMinute = (int)date("i");
$currentTime = ($currentHour * 60) + $currentMinute; 

$minTimeAmbil = 7 * 60; 
$maxTimeKumpul = (21 * 60) + 15; 

if (array_key_exists($barcode, $students)) {
    $student = $students[$barcode];
    $statusFile = 'laptop_status.txt';
    $logFile = 'laptop_log.txt';

    $laptopStatus = [];
    if (file_exists($statusFile)) {
        $lines = file($statusFile, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $data = explode('|', $line);
            if (count($data) >= 6) {
                $laptopStatus[$data[0]] = [
                    'name' => $data[1],
                    'class' => $data[2],
                    'status' => $data[3],
                    'takeTime' => $data[4],
                    'returnTime' => $data[5]
                ];
            }
        }
    }
    
    if ($action == 'ambil') {
        if ($currentTime < $minTimeAmbil) {
            $message = "❌ <span style='color:red;'>Pengambilan laptop hanya diperbolehkan mulai pukul 07:00!</span>";
        } else {
            if (isset($laptopStatus[$barcode]) && $laptopStatus[$barcode]['status'] == 'Diambil') {
                $message = "❌ <span style='color:red;'>Anda sudah mengambil laptop!</span>";
            } else {
                $laptopStatus[$barcode] = [
                    'name' => $student['name'],
                    'class' => $student['class'],
                    'status' => 'Diambil',
                    'takeTime' => $time,
                    'returnTime' => '-'
                ];
                
                $message = "✅ <strong>Pengambilan Laptop Berhasil</strong><br>" .
                           "<strong>NIS:</strong> {$barcode}<br>" .
                           "<strong>Nama:</strong> {$student['name']}<br>" .
                           "<strong>Kelas:</strong> {$student['class']}<br>" .
                           "<strong>Waktu Ambil:</strong> {$time}";
                
                $log = "{$time}|Ambil|{$barcode}|{$student['name']}|{$student['class']}\n";
                file_put_contents($logFile, $log, FILE_APPEND);
            }
        }
    } 
    else if ($action == 'kumpul') {
        if (!isset($laptopStatus[$barcode]) || $laptopStatus[$barcode]['status'] != 'Diambil') {
            $message = "❌ <span style='color:red;'>Anda belum mengambil laptop!</span>";
        } else {
            $isLate = $currentTime > $maxTimeKumpul;
            $status = $isLate ? 'Terkumpul (Terlambat)' : 'Terkumpul';
            
            $laptopStatus[$barcode]['status'] = $status;
            $laptopStatus[$barcode]['returnTime'] = $time;
            
            $message = "✅ <strong>Pengumpulan Laptop Berhasil</strong><br>" .
                       "<strong>NIS:</strong> {$barcode}<br>" .
                       "<strong>Nama:</strong> {$student['name']}<br>" .
                       "<strong>Kelas:</strong> {$student['class']}<br>" .
                       "<strong>Waktu Kumpul:</strong> {$time}";
            
            if ($isLate) {
                $message .= "<br><span style='color:orange;'><strong>Peringatan:</strong> Pengumpulan terlambat (batas waktu 21:15)</span>";
            }
            
            $statusLog = $isLate ? "Kumpul (Terlambat)" : "Kumpul";
            $log = "{$time}|{$statusLog}|{$barcode}|{$student['name']}|{$student['class']}\n";
            file_put_contents($logFile, $log, FILE_APPEND);
        }
    }
    
    $statusData = '';
    foreach ($laptopStatus as $nis => $data) {
        $statusData .= "{$nis}|{$data['name']}|{$data['class']}|{$data['status']}|{$data['takeTime']}|{$data['returnTime']}\n";
    }
    file_put_contents($statusFile, $statusData);
    
} else {
    $message = "❌ <span style='color:red;'>NIS tidak dikenal!</span>";
}
header("Location: index.php?action={$action}&message=" . urlencode($message));
exit;