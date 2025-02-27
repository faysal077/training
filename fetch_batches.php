<?php
include 'db_connection.php';

if (!isset($_GET['training_id'])) {
    echo "<div class='alert alert-danger'>Training ID is missing.</div>";
    exit;
}

$training_id = intval($_GET['training_id']);
$query = "SELECT * FROM batches WHERE training_id = $training_id ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='container mt-4'>";
    echo "<div class='row g-3'>"; // Bootstrap Grid System
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='col-md-6 col-lg-4'>
                <div class='card shadow-sm border-0 rounded-3'>
                    <div class='card-body'>
                        <h5 class='card-title fw-bold'>Batch: {$row['batch_number']}</h5>
                        <p class='card-text mb-1'><strong>Duration:</strong> {$row['start_date']} to {$row['end_date']}</p>
                        <p class='card-text mb-1'><strong>Fiscal Year:</strong> {$row['fiscal_year']}</p>
                        <p class='card-text mb-1'><strong>Organizer:</strong> {$row['organizer']}</p>
                        <p class='card-text mb-3'><strong>Type:</strong> {$row['training_type']}</p>
                        <div class='d-flex justify-content-between'>
                            <a href='participants_list.php?training_id={$training_id}&batch_id={$row['id']}' class='btn btn-sm btn-success'>Participants</a>
                            <a href='financial_clearance.php?training_id={$training_id}&batch_id={$row['id']}' class='btn btn-sm btn-warning'>Financial Clearance</a>
                            <a href='attachments.php?training_id={$training_id}&batch_id={$row['id']}' class='btn btn-sm btn-info'>Attachments</a>
                        </div>
                    </div>
                </div>
              </div>";
    }
    echo "</div></div>";
} else {
    echo "<div class='alert alert-warning text-center mt-4'>No batches available.</div>";
}
?>
