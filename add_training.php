<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Training</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
</head>
<body>

<?php include 'navbar.php'; ?> <!-- Include the navbar -->

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center">
        <button class="btn btn-success" id="openAddTrainingModal">Add New Training</button>
    </div>

    <!-- Modal for Adding New Training -->
    <div id="addTrainingModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Training</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTrainingForm">
                        <div class="mb-3">
                            <label for="training_title" class="form-label">Training Title</label>
                            <input type="text" id="training_title" name="training_title" class="form-control" required>
                        </div>
						<div class="mb-3">
                            <label for="organizer" class="form-label">প্রশিক্ষণ আয়োজনকারী</label>
                            <input type="text" id="organizer" name="organizer" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="training_type" class="form-label">প্রশিক্ষণের ধরণ</label>
                            <select id="training_type" name="training_type" class="form-select" required>
                                <option value="">Select</option>
                                <option value="ইন-হাউজ/অভ্যন্তরীণ">ইন-হাউজ/অভ্যন্তরীণ</option>
                                <option value="স্থানীয়">স্থানীয়</option>
                                <option value="বৈদেশিক">বৈদেশিক</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Training</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h3 class="my-4">Existing Trainings</h3>
    <div id="trainings">
        <?php include 'fetch_trainings.php'; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Open Modal
    document.getElementById("openAddTrainingModal").addEventListener("click", function() {
        let modal = new bootstrap.Modal(document.getElementById("addTrainingModal"));
        modal.show();
    });

    // AJAX form submission
    $(document).ready(function(){
        $("#addTrainingForm").submit(function(event){
            event.preventDefault();

            $.ajax({
                url: "create_training.php",
                type: "POST",
                data: $(this).serialize(),
                success: function(response){
                    if(response.trim() === "success"){
                        alert("Training added successfully!");
                        $("#addTrainingModal").modal("hide"); 
                        $("#addTrainingForm")[0].reset(); 
                        $("#trainings").load("fetch_trainings.php"); // Refresh the training list
                    } else {
                        alert("Error: " + response);
                    }
                }
            });
        });
    });
</script>

</body>
</html>
