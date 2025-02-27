<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_id = intval($_POST['batch_id']);
    $amount_spent = floatval($_POST['amount_spent']);
    $advance_receiver = trim($_POST['advance_receiver']);
    $adjustment_info = trim($_POST['adjustment_info']);

    if ($amount_spent > 0 && !empty($advance_receiver) && !empty($adjustment_info)) {
        $insert_query = "INSERT INTO financial_clearance (batch_id, amount_spent, advance_receiver, adjustment_info) 
                         VALUES ('$batch_id', '$amount_spent', '$advance_receiver', '$adjustment_info')";
        if (mysqli_query($conn, $insert_query)) {
            echo "Financial clearance added successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Please fill all fields correctly.";
    }
}
?>
