<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $training_id = intval($_POST['training_id']);
    $batch_id = intval($_POST['batch_id']);
    $file = $_FILES['file'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file["name"]);

    if (!empty($title) && move_uploaded_file($file["tmp_name"], $target_file)) {
        $query = "INSERT INTO attachments (training_id, batch_id, title, file_path) 
                  VALUES ('$training_id', '$batch_id', '$title', '$target_file')";
        if (mysqli_query($conn, $query)) {
            echo "File uploaded successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload file. Please check the file and title.";
    }
}
?>
