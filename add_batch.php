<?php
include 'db_connection.php';

if (!isset($_GET['training_id'])) {
    echo "Training ID is missing.";
    exit;
}

$training_id = intval($_GET['training_id']);

// Fetch training title
$training_query = "SELECT title FROM trainings WHERE id = $training_id";
$training_result = mysqli_query($conn, $training_query);
$training = mysqli_fetch_assoc($training_result);

if (!$training) {
    echo "Training not found.";
    exit;
}

$training_title = $training['title'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $training_title; ?> - Add Batch</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container my-4">
    <h2><?php echo $training_title; ?></h2>
    <button class="btn btn-success my-3" id="openAddBatchModal">Add New Batch</button>
    
    <div id="addBatchModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBatchForm">
                        <input type="hidden" name="training_id" value="<?php echo $training_id; ?>">

                        <div class="mb-3">
                            <label for="training_title" class="form-label">Training Title</label>
                            <input type="text" id="training_title" name="training_title" class="form-control" value="<?php echo $training_title; ?>" readonly>
                        </div>

                        <div class="mb-3 d-flex">
                            <div class="w-50">
                                <label for="start_date" class="form-label">Training Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" required>
                            </div>
                            <div class="w-50">
                                <label for="end_date" class="form-label">Training End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3 d-flex">
                            <div class="w-50">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" id="start_time" name="start_time" class="form-control" required>
                            </div>
                            <div class="w-50">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" id="end_time" name="end_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="total_training_time" class="form-label">Total Training Time (Hours)</label>
                            <input type="text" id="total_training_time" name="total_training_time" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="fiscal_year" class="form-label">Fiscal Year</label>
                            <input type="text" id="fiscal_year" name="fiscal_year" class="form-control" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Batch</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h3 class="my-4">Existing Batches</h3>
    <div id="batches">
        <?php include 'fetch_batches.php'; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Open Add Batch Modal
    document.getElementById("openAddBatchModal").addEventListener("click", function() {
        let modal = new bootstrap.Modal(document.getElementById("addBatchModal"));
        modal.show();
    });

    // Auto-calculate Fiscal Year
    function calculateFiscalYear() {
        let startDate = document.getElementById("start_date").value;
        let endDate = document.getElementById("end_date").value;
        
        if (startDate && endDate) {
            let startYear = new Date(startDate).getFullYear();
            let startMonth = new Date(startDate).getMonth() + 1; // Months are 0-based
            
            let fiscalYearStart = (startMonth >= 7) ? startYear : startYear - 1;
            let fiscalYearEnd = fiscalYearStart + 1;
            document.getElementById("fiscal_year").value = fiscalYearStart + "-" + fiscalYearEnd;
        }
    }

    document.getElementById("start_date").addEventListener("change", calculateFiscalYear);
    document.getElementById("end_date").addEventListener("change", calculateFiscalYear);

    // Calculate total training time
    function calculateTotalTrainingTime() {
    let startDate = document.getElementById("start_date").value;
    let endDate = document.getElementById("end_date").value;
    let startTime = document.getElementById("start_time").value;
    let endTime = document.getElementById("end_time").value;

    if (startDate && endDate && startTime && endTime) {
        let startDateObj = new Date(startDate);
        let endDateObj = new Date(endDate);
        
        // Calculate total days of training (including both start and end date)
        let totalDays = (endDateObj - startDateObj) / (1000 * 60 * 60 * 24) + 1;

        let startTimeObj = new Date("1970-01-01T" + startTime);
        let endTimeObj = new Date("1970-01-01T" + endTime);
        
        // Calculate daily training hours
        let dailyHours = (endTimeObj - startTimeObj) / (1000 * 60 * 60);
        
        if (dailyHours < 0) {
            document.getElementById("total_training_time").value = "Invalid Time Selection";
            return;
        }

        // Total training hours = daily hours × total days
        let totalTrainingHours = dailyHours * totalDays;
        document.getElementById("total_training_time").value = totalTrainingHours.toFixed(2);
    }
}


    document.getElementById("start_time").addEventListener("change", calculateTotalTrainingTime);
    document.getElementById("end_time").addEventListener("change", calculateTotalTrainingTime);

    function showToast(message, bgColor) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top", 
            position: "right", 
            backgroundColor: bgColor,
        }).showToast();
    }

    $(document).ready(function(){
        $("#addBatchForm").submit(function(event){
            event.preventDefault();
            $.ajax({
                url: "create_batch.php",
                type: "POST",
                data: $(this).serialize(),
                success: function(response){
                    if(response.trim() === "success"){
                        showToast("Batch added successfully!", "#28a745");
                        $("#addBatchModal").modal("hide"); 
                        $("#addBatchForm")[0].reset(); 
                        $("#batches").load("fetch_batches.php?training_id=<?php echo $training_id; ?>");
                    } else {
                        showToast("Error: " + response, "#dc3545");
                    }
                }
            });
        });
    });
</script>
</body>
</html>
