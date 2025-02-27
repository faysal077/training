<?php
include 'db_connection.php';

$query = "SELECT * FROM trainings ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="container mt-4">';
    echo '<div class="row">'; // Bootstrap grid system

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='col-md-4 mb-4'>";
        echo "<div class='card shadow-sm h-100'>";
        echo "<div class='card-body text-center'>";
        echo "<h5 class='card-title'>{$row['title']}</h5>";
        echo "<p class='card-text'><strong>Created at:</strong> {$row['created_at']}</p>";
        echo "<button class='btn btn-primary w-100' onclick=\"window.location.href='add_batch.php?training_id={$row['id']}'\">View Details</button>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    echo '</div>'; // Close row
    echo '</div>'; // Close container
} else {
    echo '<p class="text-center mt-3">No trainings available.</p>';
}
?>
