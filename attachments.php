<?php
include 'db_connection.php';

if (!isset($_GET['training_id']) || !isset($_GET['batch_id'])) {
    echo "Training ID or Batch ID is missing.";
    exit;
}

$training_id = intval($_GET['training_id']);
$batch_id = intval($_GET['batch_id']);

// Fetch Attachments for this Batch
$attachments = [];
$query = "SELECT * FROM attachments WHERE batch_id = '$batch_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $attachments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Attachments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container my-4">
    <h2 class="text-center">Attachments for Batch ID: <?php echo $batch_id; ?></h2>
    
    <!-- Button to Open Modal -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload Attachment</button>
    
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Attachment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Document Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload File</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                        <input type="hidden" name="training_id" value="<?php echo $training_id; ?>">
                        <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>">
                        <button type="submit" class="btn btn-success w-100">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Display Uploaded Attachments -->
    <h3 class="text-center mt-4">Uploaded Documents</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Document Title</th>
                <th>File</th>
                <th>Uploaded At</th>
            </tr>
        </thead>
        <tbody id="attachmentList">
            <?php if (!empty($attachments)): ?>
                <?php foreach ($attachments as $attachment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($attachment['title']); ?></td>
                        <td><a href="<?php echo $attachment['file_path']; ?>" target="_blank">View File</a></td>
                        <td><?php echo htmlspecialchars($attachment['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-danger">No attachments found for this batch.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#uploadForm').submit(function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: 'upload_attachment.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response);
                location.reload();
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
