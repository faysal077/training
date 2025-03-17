
<?php
/*
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_id = intval($_POST['training_id']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);

    // Calculate fiscal year (July - June)
    $startYear = date('Y', strtotime($start_date));
    $startMonth = date('m', strtotime($start_date));
    $fiscalYearStart = ($startMonth >= 7) ? $startYear : ($startYear - 1);
    $fiscalYearEnd = $fiscalYearStart + 1;
    $fiscal_year = $fiscalYearStart . "-" . $fiscalYearEnd;

    // Calculate total training hours
    $startDateTime = new DateTime("$start_date $start_time");
    $endDateTime = new DateTime("$end_date $end_time");
    $interval = $startDateTime->diff($endDateTime);
    $total_training_hours = ($interval->days + 1) * ($interval->h + ($interval->i / 60));

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
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    // Insert the new batch with additional fields
    $insert_query = "INSERT INTO batches (training_id, start_date, end_date, fiscal_year, batch_number, start_time, end_time, total_training_hours) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insert_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssissd", $training_id, $start_date, $end_date, $fiscal_year, $next_batch_number, $start_time, $end_time, $total_training_hours);

        if (mysqli_stmt_execute($stmt)) {
            echo "Batch created successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
*/
?>

<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_id = intval($_POST['training_id']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    $total_training_hours = floatval($_POST['total_training_time']);

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

    // Insert the new batch with batch_number, start_time, end_time, and total_training_hours
    $insert_query = "INSERT INTO batches (training_id, start_date, end_date, fiscal_year, batch_number, start_time, end_time, total_training_hours) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insert_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssissd", $training_id, $start_date, $end_date, $fiscal_year, $next_batch_number, $start_time, $end_time, $total_training_hours);
        
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