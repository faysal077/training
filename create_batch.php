<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_id = intval($_POST['training_id']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

    // Calculate fiscal year (July - June)
    $startYear = date('Y', strtotime($start_date));
    $startMonth = date('m', strtotime($start_date));
    $fiscalYearStart = ($startMonth >= 7) ? $startYear : ($startYear - 1);
    $fiscalYearEnd = $fiscalYearStart + 1;
    $fiscal_year = $fiscalYearStart . "-" . $fiscalYearEnd;

    // Get the highest batch_number for the given training_id
    $query = "SELECT COALESCE(MAX(batch_number), 0) + 1 AS next_batch_number FROM batches WHERE training_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $training_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $next_batch_number);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "error: " . mysqli_error($conn);
        exit;
    }

    // Insert the new batch with batch_number
    $insert_query = "INSERT INTO batches (training_id, start_date, end_date, fiscal_year, batch_number) 
                     VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insert_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssi", $training_id, $start_date, $end_date, $fiscal_year, $next_batch_number);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
