<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['training_title']);
	$organizer = mysqli_real_escape_string($conn, $_POST['organizer']);
    $training_type = mysqli_real_escape_string($conn, $_POST['training_type']);

    // Insert only the title into the database
    $query = "INSERT INTO trainings (title, organizer, training_type) VALUES ('$title', '$organizer', '$training_type')";

    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
