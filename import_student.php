<?php
// Include database connection
require_once 'config.php';

// Array of students from your proses.php file
$students = [
    "0079787882" => ["name" => "Rayhan Nulhafiz", "class" => "XI D"],
    // Add more students here
];

// Insert students into the database
$inserted = 0;
$skipped = 0;

foreach ($students as $nis => $student) {
    // Check if student already exists
    $check_sql = "SELECT * FROM students WHERE nis = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $nis);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) == 0) {
        // Student doesn't exist, insert them
        $insert_sql = "INSERT INTO students (nis, name, class) VALUES (?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "sss", $nis, $student['name'], $student['class']);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $inserted++;
            echo "Berhasil menambahkan siswa: {$student['name']} ({$nis})<br>";
        } else {
            echo "Gagal menambahkan siswa: {$student['name']} ({$nis}) - " . mysqli_error($conn) . "<br>";
        }
    } else {
        $skipped++;
        echo "Siswa sudah ada: {$student['name']} ({$nis})<br>";
    }
}

echo "<br>Selesai! Berhasil menambahkan {$inserted} siswa, {$skipped} siswa dilewati (sudah ada).";

mysqli_close($conn);
