<?php
include 'db_connection.php';

if (!isset($_GET['training_id']) || !isset($_GET['batch_id'])) {
    echo "Training ID or Batch ID is missing.";
    exit;
}

$training_id = intval($_GET['training_id']);
$batch_id = intval($_GET['batch_id']);

// Fetch financial clearance records for the batch
$financial_records = [];
$query = "SELECT * FROM financial_clearance WHERE batch_id = '$batch_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $financial_records[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Clearance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container my-4">
    <h2 class="text-center">Financial Clearance for Batch ID: <?php echo $batch_id; ?></h2>

    <!-- Button to trigger modal -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#financialModal">Add Financial Clearance</button>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="financialModal" tabindex="-1" aria-labelledby="financialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="financialModalLabel">Add Financial Clearance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="financialForm">
                        <div class="row">
                            <!-- ব্যয়িত অর্থের পরিমাণ and অগ্রিম গ্রহণকারীর নাম in one row -->
                            <div class="col-md-6 mb-3">
                                <label for="amount_spent" class="form-label">ব্যয়িত অর্থের পরিমাণ</label>
                                <input type="number" step="0.01" name="amount_spent" id="amount_spent" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="advance_receiver" class="form-label">অগ্রিম গ্রহণকারীর নাম</label>
                                <input type="text" name="advance_receiver" id="advance_receiver" class="form-control" required>
                            </div>
                        </div>
                        <!-- সমন্বয়ের তথ্য in the next row -->
                        <div class="mb-3">
                            <label for="adjustment_info" class="form-label">সমন্বয়ের তথ্য</label>
                            <textarea name="adjustment_info" id="adjustment_info" class="form-control" required></textarea>
                        </div>
                        <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>">
                        <button type="submit" class="btn btn-success w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Display Financial Clearance Records -->
    <h3 class="text-center mt-4">Financial Clearance Records</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ব্যয়িত অর্থের পরিমাণ</th>
                <th>অগ্রিম গ্রহণকারীর নাম</th>
                <th>সমন্বয়ের তথ্য</th>
                <th>তারিখ</th>
            </tr>
        </thead>
        <tbody id="recordsTable">
            <?php if (!empty($financial_records)): ?>
                <?php foreach ($financial_records as $record): ?>
                    <tr>
                        <td><?php echo number_format($record['amount_spent'], 2); ?></td>
                        <td><?php echo htmlspecialchars($record['advance_receiver']); ?></td>
                        <td><?php echo htmlspecialchars($record['adjustment_info']); ?></td>
                        <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-danger">No financial clearance records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $("#financialForm").on("submit", function(event){
        event.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: "process_financial_clearance.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                alert(response);
                $("#financialModal").modal("hide");
                location.reload(); // Reload the page to update records
            }
        });
    });
});
</script>

</body>
</html>
