<?php
include 'db_connection.php';

// Get input values
$training_id = $_GET['training_id'] ?? null;
$batch_id = $_GET['batch_id'] ?? null;
$batch_number = $_GET['batch_number'] ?? '';

echo "Training ID: $training_id;";
echo " Batch ID: $batch_id;";
echo " Batch Number: $batch_number;";

// Ensure Training ID is provided
if (!$training_id) {
    die("Error: Training ID is required.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $office = $_POST['office'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $official_id = $_POST['official_id']; // New field

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Step 1: Insert into Batches if batch_id is not provided
        if (!$batch_id) {
            $batch_query = "INSERT INTO batches (training_id, start_date, end_date, fiscal_year, organizer, training_type) 
                            VALUES (?, NULL, NULL, NULL, NULL, NULL)";
            $stmt = mysqli_prepare($conn, $batch_query);
            mysqli_stmt_bind_param($stmt, "i", $training_id);
            mysqli_stmt_execute($stmt);
            $batch_id = mysqli_insert_id($conn); // Get the new batch ID
        }

        // Step 2: Insert into Participants
        $participant_query = "INSERT INTO participants (training_id, batch_id, batch_number, name, designation, office_address, gender, contact, email, Official_ID) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $participant_query);
        mysqli_stmt_bind_param($stmt, "iiisssssss", $training_id, $batch_id, $batch_number, $name, $designation, $office, $gender, $contact, $email, $official_id);
        mysqli_stmt_execute($stmt);

        // Commit transaction
        mysqli_commit($conn);

        header("Location: participants_list.php?training_id=$training_id&batch_id=$batch_id&success=1");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback if an error occurs
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Participant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .form-label {
            text-align: right;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?> <!-- Include the navbar -->
    <div class="container my-4">
        <h3>Add New Participant</h3>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Error adding participant. Please try again.</div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="row mb-3 align-items-center">
                <label for="name" class="col-md-3 form-label">Name</label>
                <div class="col-md-9">
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="official_id" class="col-md-3 form-label">আই ডি নম্বর</label>
                <div class="col-md-9">
                    <input type="text" name="official_id" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="designation" class="col-md-3 form-label">Designation</label>
                <div class="col-md-9">
                    <input type="text" name="designation" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="office" class="col-md-3 form-label">Office</label>
                <div class="col-md-9">
                    <input type="text" name="office" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="gender" class="col-md-3 form-label">Gender</label>
                <div class="col-md-9">
                    <select name="gender" class="form-control">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="contact" class="col-md-3 form-label">Contact</label>
                <div class="col-md-9">
                    <input type="text" name="contact" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="email" class="col-md-3 form-label">Email</label>
                <div class="col-md-9">
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <button type="submit" class="btn btn-primary">Add Participant</button>
                </div>
            </div>
        </form>
    </div>
	   <script>
    // List of designations
    const designations = [
        'মহাব্যবস্থাপক', 
        'উপমহাব্যবস্থাপক', 
        'সহকারী মহাব্যবস্থাপক', 
        'ব্যবস্থাপক', 
        'সম্প্রসারণ কর্মকর্তা'
    ];

    const offices = [
        'বিসিক প্রধান কার্যালয়',
        'বিসিক আঞ্চলিক কার্যালয় রাজশাহী',
        'বিসিক আঞ্চলিক কার্যালয় খুলনা',
        'বিসিক আঞ্চলিক কার্যালয় চট্টগ্রাম',
        'বিসিক আঞ্চলিক কার্যালয় ঢাকা',
        'বিসিক জেলা কার্যালয়, রাজবাড়ী',
        'বিসিক জেলা কার্যালয়, নারায়ণগঞ্জ'
    ];

    function populateDropdown(input, dropdown, items, isDesignation = false) {
        dropdown.innerHTML = ''; // Clear previous items
        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'dropdown-item';
            li.textContent = item;
            li.style.cursor = 'pointer';
            li.onclick = () => {
                if (isDesignation) {
                    updateDesignation(item);
                } else {
                    input.value = item; // Ensure office input is updated correctly
                }
                dropdown.innerHTML = ''; // Close dropdown
            };
            dropdown.appendChild(li);
        });
    }

    // Function to update designation input with (ভা:) if selected
    function updateDesignation(selectedDesignation) {
        const actingCheck = document.getElementById('actingCheck');
        const designationInput = document.getElementById('designationInput');
        
        if (actingCheck.checked) {
            designationInput.value = selectedDesignation + ' (ভা:)';
        } else {
            designationInput.value = selectedDesignation;
        }
    }

    // Handle radio button toggle
    document.getElementById('actingCheck').addEventListener('change', function() {
        const designationInput = document.getElementById('designationInput');
        if (this.checked && designationInput.value) {
            designationInput.value += ' (ভা:)';
        } else if (!this.checked) {
            designationInput.value = designationInput.value.replace(' (ভা:)', '');
        }
    });

    // Designation dropdown logic
    const designationInput = document.getElementById('designationInput');
    const designationDropdown = document.getElementById('designationDropdown');
    designationInput.addEventListener('input', () => {
        const query = designationInput.value.toLowerCase();
        const filtered = designations.filter(designation =>
            designation.toLowerCase().includes(query)
        );
        populateDropdown(designationInput, designationDropdown, filtered, true);
    });

    populateDropdown(designationInput, designationDropdown, designations, true);

    // Office dropdown logic
    const officeInput = document.getElementById('officeInput');
    const officeDropdown = document.getElementById('officeDropdown');
    officeInput.addEventListener('input', () => {
        const query = officeInput.value.toLowerCase();
        const filtered = offices.filter(office =>
            office.toLowerCase().includes(query)
        );
        populateDropdown(officeInput, officeDropdown, filtered, false);
    });

    populateDropdown(officeInput, officeDropdown, offices, false);
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
