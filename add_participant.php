<?php
include 'db_connection.php';

// Get input values
$training_id = $_GET['training_id'] ?? null;
$batch_id = $_GET['batch_id'] ?? null;
$batch_number = $_GET['batch_number'] ?? '';
// echo $training_id," ",$batch_id," ",$batch_number;
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
    $Official_ID = $_POST['Official_ID']; // New field

    // Step 1: Check if the person is already enrolled in any batch for this training
    $check_query = "SELECT batch_id, batch_number FROM participants WHERE training_id = ? AND Official_ID = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "is", $training_id, $Official_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // If a record is found, show a Toastify alert with the batch number
        $existing_participant = mysqli_fetch_assoc($result);
        $existing_batch_number = $existing_participant['batch_number'];
        
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Toastify({
                        text: 'This person is already enrolled in this training in Batch No: $existing_batch_number',
                        duration: 5000,
                        gravity: 'top', 
                        position: 'right', 
                        backgroundColor: '#ff4d4d',
                        close: true
                    }).showToast();
                });
              </script>";
    } else {
        // Step 2: Insert into Batches if batch_id is not provided
        if (!$batch_id) {
            $batch_query = "INSERT INTO batches (training_id, start_date, end_date, fiscal_year, organizer, training_type) 
                            VALUES (?, NULL, NULL, NULL, NULL, NULL)";
            $stmt = mysqli_prepare($conn, $batch_query);
            mysqli_stmt_bind_param($stmt, "i", $training_id);
            mysqli_stmt_execute($stmt);
            $batch_id = mysqli_insert_id($conn); // Get the new batch ID
        }

        // Step 2.5: Retrieve total_training_hours from batches
        $hours_query = "SELECT total_training_hours FROM batches WHERE id = ?";
		echo " ",$hours_query;
        $stmt = mysqli_prepare($conn, $hours_query);
        mysqli_stmt_bind_param($stmt, "i", $batch_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $total_training_hours = $row['total_training_hours'] ?? 0; // Default to 0 if NULL

        // Step 3: Insert into Participants
        $participant_query = "INSERT INTO participants (training_id, batch_id, batch_number, name, designation, office_address, gender, contact, email, Official_ID, total_training_hours) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $participant_query);
        mysqli_stmt_bind_param($stmt, "iiisssssssi", $training_id, $batch_id, $batch_number, $name, $designation, $office, $gender, $contact, $email, $Official_ID, $total_training_hours);
        mysqli_stmt_execute($stmt);

        // Commit transaction
        mysqli_commit($conn);

        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Toastify({
                        text: 'Participant added successfully!',
                        duration: 5000,
                        gravity: 'top', 
                        position: 'right', 
                        backgroundColor: '#28a745',
                        close: true
                    }).showToast();
                });
              </script>";

        // Redirect after successful entry
        header("Location: participants_list.php?training_id=$training_id&batch_id=$batch_id&success=1");
        exit();
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
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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
				<label class="col-md-3 form-label">আই ডি নম্বর</label>
				<div class="col-md-6">
					<input type="text" name="Official_ID_1" id="Official_ID_1" class="form-control" required>
				</div>
				<div class="col-md-3">
					<button type="button" id="searchId" class="btn btn-primary">Search</button>
				</div>
			</div>
            <div class="row mb-3 align-items-center">
                <label for="name" class="col-md-3 form-label">Name</label>
                <div class="col-md-9">
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="Official_ID" class="col-md-3 form-label">আই ডি নম্বর</label>
                <div class="col-md-9">
                    <input type="text" id="Official_ID" name="Official_ID" class="form-control" required> <!-- Remove required --!>
                </div>
            </div>
            
                        <!-- Designation Field -->
            <div class="row mb-3 align-items-center">
                <label for="designation" class="col-md-3 form-label">Designation</label>
                <div class="col-md-9">
                    <div class="dropdown">
                        <input 
                            type="text" 
							id="designation"
                            name="designation" 
                            id="designationInput" 
                            class="form-control dropdown-toggle" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false" 
                            placeholder="Search designation..."
                            autocomplete="off"
                            required
                        >
                        <ul class="dropdown-menu w-100" id="designationDropdown">
                            <!-- Options will be populated dynamically -->
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Radio Button for ভারপ্রাপ্ত -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <div class="form-check">
                        <input type="radio" id="actingCheck" name="acting" class="form-check-input">
                        <label for="actingCheck" class="form-check-label">ভারপ্রাপ্ত</label>
                    </div>
                </div>
            </div>

            <!-- Office Field -->
            <div class="row mb-3 align-items-center">
                <label for="office" class="col-md-3 form-label">Office</label>
                <div class="col-md-9">
                    <div class="dropdown">
                        <input 
                            type="text" 
							id="office"
                            name="office" 
                            id="officeInput" 
                            class="form-control dropdown-toggle" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false" 
                            placeholder="Search office..."
                            autocomplete="off"
                            required
                        >
                        <ul class="dropdown-menu w-100" id="officeDropdown">
                            <!-- Options will be populated dynamically -->
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="gender" class="col-md-3 form-label">Gender</label>
                <div class="col-md-9">
                    <select id="gender" name="gender" class="form-control">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="contact" class="col-md-3 form-label">Contact</label>
                <div class="col-md-9">
                    <input type="text" name="contact" id="contact" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3 align-items-center">
                <label for="email" class="col-md-3 form-label">Email</label>
                <div class="col-md-9">
                    <input type="email" id="email" name="email" class="form-control" required>
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
	<!--
	<script>
    // List of designations
    const designations = [
        "চেয়ারম্যান",
		"পরিচালক",
		"সচিব",
		"মহাব্যবস্থাপক", 
        "উপমহাব্যবস্থাপক", 
        "সহকারী মহাব্যবস্থাপক", 
        "সম্প্রসারণ কর্মকর্তা",
		"আঞ্চলিক পরিচালক",
		"নিয়ন্ত্রক (হিসাব ও অর্থ)",
		"প্রধান প্রকৌশলী",
		"উপমহাব্যবস্থাপক",
		"প্রধান নিরীক্ষা কর্মকর্তা",
		"আইটি সেল প্রধান",
		"উপ-সচিব",
		"সহকারী মহাব্যবস্থাপক",
		"ব্যবস্থাপক",
		"উপপ্রধান নিরীক্ষা কর্মকর্তা",
		"উপনিয়ন্ত্রক",
		"উপপ্রধান প্রকৌশলী",
		"উপপ্রধান মেডিক্যাল অফিসার",
		"উপব্যবস্থাপক",
		"প্রোগ্রামার",
		"সহকারী প্রোগ্রামার",
		"সহকারী মেইনটেন্যান্স ইঞ্জিনিয়ার",
		"বিশেষজ্ঞ",
		"সহকারী নিয়ন্ত্রক (হিসাব ও অর্থ)",
		"সহকারী প্রধান নিরীক্ষা কর্মকর্তা",
		"নির্বাহী প্রকৌশলী",
		"প্রশাসনিক কর্মকর্তা",
		"কর্মীব্যবস্থাপনা কর্মকর্তা",
		"পরিকল্পনা কর্মকর্তা",
		"গবেষণা কর্মকর্তা",
		"উর্ধ্বতন সমন্বয় কর্মকর্তা",
		"প্রটোকল অফিসার",
		"প্রশিক্ষণ কর্মকর্তা",
		"হিসাব রক্ষণ কর্মকর্তা",
		"অডিট অফিসার",
		"বাজেট অফিসার",
		"ফাইস্যান্স অফিসার",
		"ক্রেডিট অফিসার",
		"প্রযুক্তি কর্মকর্তা",
		"সহকারী প্রকৌশলী",
		"ডাটা এনালিষ্ট",
		"সম্প্রসারণ কর্মকর্তা",
		"এনালিষ্ট",
		"মাননিয়ন্ত্রণ কর্মকর্তা",
		"মার্কেটিং কর্মকর্তা",
		"প্রমোশন কর্মকর্তা",
		"জরীপ ও তথ্য কর্মকর্তা",
		"মেডিক্যাল অফিসার",
		"শিল্পনগরী কর্মকর্তা",
		"টেকনিক্যাল অফিসার",
		"ড্রাফটস্ম্যান",
		"রিসিপশন অফিসার",
		"সিকিউরিটি অফিসার",
		"ষ্টোর অফিসার",
		"রেকর্ড অফিসার",
		"সহকারী হিসাব রক্ষণ কর্মকর্তা",
		"সমন্বয় কর্মকর্তা",
		"লাইসেনসিং অফিসার",
		"সহকারী প্রশাসনিক কর্মকর্তা",
		"সহকারী নিরীক্ষা কর্মকর্তা",
		"সহকারী লাইব্রেরীয়ান",
		"ষ্টেনোগ্রাফার",
		"উচ্চমান সহকারী",
		"ষ্টোর সহকারী",
		"কেয়ার টেকার",
		"পরিদর্শক",
		"মাননিয়ন্ত্রণ সহকারী",
		"নিরাপত্তা পরিদর্শক",
		"বাজেট সহকারী",
		"হিসাব সহকারী",
		"ফাইনান্স সহকারী",
		"নিরীক্ষা সহকারী",
		"হিসাব রক্ষণ তথা কোষাধ্যক্ষ",
		"ক্যাশিয়ার",
		"ইলেকট্রিশিয়ান",
		"কম্পাউন্ডার",
		"ক্লিনিক্যাল সহকারী",
		"সাঁট-মুদ্রাক্ষরিক",
		"করনিক-তথা-মুদ্রাক্ষরিক",
		"ফটোমেশিন অপারেটর",
		"ড্রাইভার",
		"টেলিফোন অপারেটর",
		"লিফটম্যান",
		"কার্পেন্টার",
		"রিসিপশনিষ্ট",
		"পাম্প চালক",
		"ডুপ্লিকেটিং মেশিন অপারেটর",
		"ক্যাশ সরকার",
		"ভেহিক্যাল মেকানিক এ্যাসিসট্যান্ট",
		"প্লাম্বার মিস্ত্রি",
		"রেকর্ড কিপার"

    ];

    const offices = [
        "পরিচালক (প্রশাসন) দপ্তর, বিসিক প্রধান কার্যালয়, ঢাকা",
		"হিসাব ও অর্থ বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"প্রশাসন বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"সম্প্রসারণ বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"প্রকল্প ব্যবস্থাপনা ও বাস্তবায়ন বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"পরিকল্পনা ও গবেষণা বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"শিল্প নগরী ও সমন্বয় শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"এমআইএস বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"বিপণন বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"নিরীক্ষা বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"দক্ষতা ও প্রযুক্তি বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"পুরকৌশল বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"নকশা কেন্দ্র, বিসিক প্রধান কার্যালয়, ঢাকা",
		"উন্নয়ন বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"প্রশিক্ষণ শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"উপকরণ শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"ঋণ প্রশাসন শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"গবেষণা শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"আইসিটি সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"আইন সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"মনিটরিং সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"লবণ সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"লেদার সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"বোর্ড শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"জনসংযোগ শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"মেডিকেল শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"লাইব্রেরি শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"আঞ্চলিক কার্যালয় ঢাকা",
		"আঞ্চলিক কার্যালয় চট্টগ্রাম",
		"আঞ্চলিক কার্যালয় খুলনা	",
		"আঞ্চলিক কার্যালয় রাজশাহী",
		"বিসিক জেলা কার্যালয়, ঢাকা",
		"বিসিক জেলা কার্যালয়, ফরিদপুর",
		"বিসিক জেলা কার্যালয়, নারায়ণগঞ্জ",
		"বিসিক জেলা কার্যালয়, জামালপুর",
		"বিসিক জেলা কার্যালয়, রাজবাড়ী",
		"বিসিক জেলা কার্যালয়, গোপালগঞ্জ",
		"বিসিক জেলা কার্যালয়,  মানিকগঞ্জ",
		"বিসিক জেলা কার্যালয়, গাজীপুর",
		"বিসিক জেলা কার্যালয়, নেত্রকোণা",
		"বিসিক জেলা কার্যালয়, শেরপুর",
		"বিসিক জেলা কার্যালয়, ময়মনসিংহ",
		"বিসিক জেলা কার্যালয়, নরসিংদী",
		"বিসিক জেলা কার্যালয়, কিশোরগঞ্জ",
		"বিসিক জেলা কার্যালয়, টাঙ্গাইল",
		"বিসিক জেলা কার্যালয়, চট্টগ্রাম",
		"বিসিক জেলা কার্যালয়, কুমিল্লা",
		"বিসিক জেলা কার্যালয়, নোয়াখালী",
		"বিসিক জেলা কার্যালয়, কক্সবাজার",
		"বিসিক জেলা কার্যালয়, চাঁদপুর",
		"বিসিক জেলা কার্যালয়, ফেনী",
		"বিসিক জেলা কার্যালয়, ব্রাহ্মণবাড়িয়া",
		"বিসিক জেলা কার্যালয়, লক্ষ্মীপুর",
		"বিসিক জেলা কার্যালয়, রাঙ্গামাটি",
		"বিসিক জেলা কার্যালয়, বান্দরবান",
		"বিসিক জেলা কার্যালয়, খাগড়াছড়ি",
		"বিসিক জেলা কার্যালয়, সিলেট",
		"বিসিক জেলা কার্যালয়, হবিগঞ্জ",
		"বিসিক জেলা কার্যালয়, মৌলভীবাজার",
		"বিসিক জেলা কার্যালয়, সুনামগঞ্জ",
		"বিসিক জেলা কার্যালয়, খুলনা",
		"বিসিক জেলা কার্যালয়, যশোর",
		"বিসিক জেলা কার্যালয়, কুষ্টিয়া",
		"বিসিক জেলা কার্যালয়, বাগেরহাট",
		"বিসিক জেলা কার্যালয়, সাতক্ষীরা",
		"বিসিক জেলা কার্যালয়, ঝিনাইদহ",
		"বিসিক জেলা কার্যালয়, মেহেরপুর",
		"বিসিক জেলা কার্যালয়, চুয়াডাঙ্গা",
		"বিসিক জেলা কার্যালয়, মাগুরা",
		"বিসিক জেলা কার্যালয়, নড়াইল",
		"বিসিক জেলা কার্যালয়, পিরোজপুর",
		"বিসিক জেলা কার্যালয়, ভোলা",
		"বিসিক জেলা কার্যালয়, বরগুনা",
		"বিসিক জেলা কার্যালয়, ঝালকাঠী",
		"বিসিক জেলা কার্যালয়, রাজশাহী",
		"বিসিক জেলা কার্যালয়, পাবনা",
		"বিসিক জেলা কার্যালয়, রংপুর",
		"বিসিক জেলা কার্যালয়, দিনাজপুর",
		"বিসিক জেলা কার্যালয়, গাইবান্ধা",
		"বিসিক জেলা কার্যালয়, চাঁপাইনবাবগঞ্জ",
		"বিসিক জেলা কার্যালয়, জয়পুরহাট",
		"বিসিক জেলা কার্যালয়, নওগাঁ",
		"বিসিক জেলা কার্যালয়, নীলফামারী",
		"বিসিক জেলা কার্যালয়, পঞ্চগড়",
		"বিসিক জেলা কার্যালয়, ঠাকুরগাঁও",
		"বিসিক জেলা কার্যালয়, কুড়িগ্রাম",
		"বিসিক জেলা কার্যালয়, সিরাজগঞ্জ",
		"বিসিক জেলা কার্যালয়, নাটোর",
		"বিসিক জেলা কার্যালয়, বগুড়া",
		"বিসিক জেলা কার্যালয়, মাদারিপুর",
		"বিসিক জেলা কার্যালয়, শরীয়তপুর",
		"বিসিক জেলা কার্যালয়, মুন্সিগঞ্জ",
		"বিসিক জেলা কার্যালয়, লালমনিরহাট",
		"বিসিক জেলা কার্যালয়, বরিশাল",
		"বিসিক জেলা কার্যালয়, পটুয়াখালী"
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
!-->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // List of designations
    const designations = [
        "চেয়ারম্যান",
		"পরিচালক",
		"সচিব",
		"মহাব্যবস্থাপক", 
        "উপমহাব্যবস্থাপক", 
        "সহকারী মহাব্যবস্থাপক", 
        "সম্প্রসারণ কর্মকর্তা",
		"আঞ্চলিক পরিচালক",
		"নিয়ন্ত্রক (হিসাব ও অর্থ)",
		"প্রধান প্রকৌশলী",
		"উপমহাব্যবস্থাপক",
		"প্রধান নিরীক্ষা কর্মকর্তা",
		"আইটি সেল প্রধান",
		"উপ-সচিব",
		"সহকারী মহাব্যবস্থাপক",
		"ব্যবস্থাপক",
		"উপপ্রধান নিরীক্ষা কর্মকর্তা",
		"উপনিয়ন্ত্রক",
		"উপপ্রধান প্রকৌশলী",
		"উপপ্রধান মেডিক্যাল অফিসার",
		"উপব্যবস্থাপক",
		"প্রোগ্রামার",
		"সহকারী প্রোগ্রামার",
		"সহকারী মেইনটেন্যান্স ইঞ্জিনিয়ার",
		"বিশেষজ্ঞ",
		"সহকারী নিয়ন্ত্রক (হিসাব ও অর্থ)",
		"সহকারী প্রধান নিরীক্ষা কর্মকর্তা",
		"নির্বাহী প্রকৌশলী",
		"প্রশাসনিক কর্মকর্তা",
		"কর্মীব্যবস্থাপনা কর্মকর্তা",
		"পরিকল্পনা কর্মকর্তা",
		"গবেষণা কর্মকর্তা",
		"উর্ধ্বতন সমন্বয় কর্মকর্তা",
		"প্রটোকল অফিসার",
		"প্রশিক্ষণ কর্মকর্তা",
		"হিসাব রক্ষণ কর্মকর্তা",
		"অডিট অফিসার",
		"বাজেট অফিসার",
		"ফাইস্যান্স অফিসার",
		"ক্রেডিট অফিসার",
		"প্রযুক্তি কর্মকর্তা",
		"সহকারী প্রকৌশলী",
		"ডাটা এনালিষ্ট",
		"সম্প্রসারণ কর্মকর্তা",
		"এনালিষ্ট",
		"মাননিয়ন্ত্রণ কর্মকর্তা",
		"মার্কেটিং কর্মকর্তা",
		"প্রমোশন কর্মকর্তা",
		"জরীপ ও তথ্য কর্মকর্তা",
		"মেডিক্যাল অফিসার",
		"শিল্পনগরী কর্মকর্তা",
		"টেকনিক্যাল অফিসার",
		"ড্রাফটস্ম্যান",
		"রিসিপশন অফিসার",
		"সিকিউরিটি অফিসার",
		"ষ্টোর অফিসার",
		"রেকর্ড অফিসার",
		"সহকারী হিসাব রক্ষণ কর্মকর্তা",
		"সমন্বয় কর্মকর্তা",
		"লাইসেনসিং অফিসার",
		"সহকারী প্রশাসনিক কর্মকর্তা",
		"সহকারী নিরীক্ষা কর্মকর্তা",
		"সহকারী লাইব্রেরীয়ান",
		"ষ্টেনোগ্রাফার",
		"উচ্চমান সহকারী",
		"ষ্টোর সহকারী",
		"কেয়ার টেকার",
		"পরিদর্শক",
		"মাননিয়ন্ত্রণ সহকারী",
		"নিরাপত্তা পরিদর্শক",
		"বাজেট সহকারী",
		"হিসাব সহকারী",
		"ফাইনান্স সহকারী",
		"নিরীক্ষা সহকারী",
		"হিসাব রক্ষণ তথা কোষাধ্যক্ষ",
		"ক্যাশিয়ার",
		"ইলেকট্রিশিয়ান",
		"কম্পাউন্ডার",
		"ক্লিনিক্যাল সহকারী",
		"সাঁট-মুদ্রাক্ষরিক",
		"করনিক-তথা-মুদ্রাক্ষরিক",
		"ফটোমেশিন অপারেটর",
		"ড্রাইভার",
		"টেলিফোন অপারেটর",
		"লিফটম্যান",
		"কার্পেন্টার",
		"রিসিপশনিষ্ট",
		"পাম্প চালক",
		"ডুপ্লিকেটিং মেশিন অপারেটর",
		"ক্যাশ সরকার",
		"ভেহিক্যাল মেকানিক এ্যাসিসট্যান্ট",
		"প্লাম্বার মিস্ত্রি",
		"রেকর্ড কিপার"

    ];

    const offices = [
        "পরিচালক (প্রশাসন) দপ্তর, বিসিক প্রধান কার্যালয়, ঢাকা",
		"হিসাব ও অর্থ বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"প্রশাসন বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"সম্প্রসারণ বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"প্রকল্প ব্যবস্থাপনা ও বাস্তবায়ন বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"পরিকল্পনা ও গবেষণা বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"শিল্প নগরী ও সমন্বয় শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"এমআইএস বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"বিপণন বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"নিরীক্ষা বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"দক্ষতা ও প্রযুক্তি বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"পুরকৌশল বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"নকশা কেন্দ্র, বিসিক প্রধান কার্যালয়, ঢাকা",
		"উন্নয়ন বিভাগ, বিসিক প্রধান কার্যালয়, ঢাকা",
		"প্রশিক্ষণ শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"উপকরণ শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"ঋণ প্রশাসন শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"গবেষণা শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"আইসিটি সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"আইন সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"মনিটরিং সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"লবণ সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"লেদার সেল, বিসিক প্রধান কার্যালয়, ঢাকা",
		"বোর্ড শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"জনসংযোগ শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"মেডিকেল শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"লাইব্রেরি শাখা, বিসিক প্রধান কার্যালয়, ঢাকা",
		"আঞ্চলিক কার্যালয় ঢাকা",
		"আঞ্চলিক কার্যালয় চট্টগ্রাম",
		"আঞ্চলিক কার্যালয় খুলনা	",
		"আঞ্চলিক কার্যালয় রাজশাহী",
		"বিসিক জেলা কার্যালয়, ঢাকা",
		"বিসিক জেলা কার্যালয়, ফরিদপুর",
		"বিসিক জেলা কার্যালয়, নারায়ণগঞ্জ",
		"বিসিক জেলা কার্যালয়, জামালপুর",
		"বিসিক জেলা কার্যালয়, রাজবাড়ী",
		"বিসিক জেলা কার্যালয়, গোপালগঞ্জ",
		"বিসিক জেলা কার্যালয়,  মানিকগঞ্জ",
		"বিসিক জেলা কার্যালয়, গাজীপুর",
		"বিসিক জেলা কার্যালয়, নেত্রকোণা",
		"বিসিক জেলা কার্যালয়, শেরপুর",
		"বিসিক জেলা কার্যালয়, ময়মনসিংহ",
		"বিসিক জেলা কার্যালয়, নরসিংদী",
		"বিসিক জেলা কার্যালয়, কিশোরগঞ্জ",
		"বিসিক জেলা কার্যালয়, টাঙ্গাইল",
		"বিসিক জেলা কার্যালয়, চট্টগ্রাম",
		"বিসিক জেলা কার্যালয়, কুমিল্লা",
		"বিসিক জেলা কার্যালয়, নোয়াখালী",
		"বিসিক জেলা কার্যালয়, কক্সবাজার",
		"বিসিক জেলা কার্যালয়, চাঁদপুর",
		"বিসিক জেলা কার্যালয়, ফেনী",
		"বিসিক জেলা কার্যালয়, ব্রাহ্মণবাড়িয়া",
		"বিসিক জেলা কার্যালয়, লক্ষ্মীপুর",
		"বিসিক জেলা কার্যালয়, রাঙ্গামাটি",
		"বিসিক জেলা কার্যালয়, বান্দরবান",
		"বিসিক জেলা কার্যালয়, খাগড়াছড়ি",
		"বিসিক জেলা কার্যালয়, সিলেট",
		"বিসিক জেলা কার্যালয়, হবিগঞ্জ",
		"বিসিক জেলা কার্যালয়, মৌলভীবাজার",
		"বিসিক জেলা কার্যালয়, সুনামগঞ্জ",
		"বিসিক জেলা কার্যালয়, খুলনা",
		"বিসিক জেলা কার্যালয়, যশোর",
		"বিসিক জেলা কার্যালয়, কুষ্টিয়া",
		"বিসিক জেলা কার্যালয়, বাগেরহাট",
		"বিসিক জেলা কার্যালয়, সাতক্ষীরা",
		"বিসিক জেলা কার্যালয়, ঝিনাইদহ",
		"বিসিক জেলা কার্যালয়, মেহেরপুর",
		"বিসিক জেলা কার্যালয়, চুয়াডাঙ্গা",
		"বিসিক জেলা কার্যালয়, মাগুরা",
		"বিসিক জেলা কার্যালয়, নড়াইল",
		"বিসিক জেলা কার্যালয়, পিরোজপুর",
		"বিসিক জেলা কার্যালয়, ভোলা",
		"বিসিক জেলা কার্যালয়, বরগুনা",
		"বিসিক জেলা কার্যালয়, ঝালকাঠী",
		"বিসিক জেলা কার্যালয়, রাজশাহী",
		"বিসিক জেলা কার্যালয়, পাবনা",
		"বিসিক জেলা কার্যালয়, রংপুর",
		"বিসিক জেলা কার্যালয়, দিনাজপুর",
		"বিসিক জেলা কার্যালয়, গাইবান্ধা",
		"বিসিক জেলা কার্যালয়, চাঁপাইনবাবগঞ্জ",
		"বিসিক জেলা কার্যালয়, জয়পুরহাট",
		"বিসিক জেলা কার্যালয়, নওগাঁ",
		"বিসিক জেলা কার্যালয়, নীলফামারী",
		"বিসিক জেলা কার্যালয়, পঞ্চগড়",
		"বিসিক জেলা কার্যালয়, ঠাকুরগাঁও",
		"বিসিক জেলা কার্যালয়, কুড়িগ্রাম",
		"বিসিক জেলা কার্যালয়, সিরাজগঞ্জ",
		"বিসিক জেলা কার্যালয়, নাটোর",
		"বিসিক জেলা কার্যালয়, বগুড়া",
		"বিসিক জেলা কার্যালয়, মাদারিপুর",
		"বিসিক জেলা কার্যালয়, শরীয়তপুর",
		"বিসিক জেলা কার্যালয়, মুন্সিগঞ্জ",
		"বিসিক জেলা কার্যালয়, লালমনিরহাট",
		"বিসিক জেলা কার্যালয়, বরিশাল",
		"বিসিক জেলা কার্যালয়, পটুয়াখালী"
    ];

    function populateDropdown(input, dropdown, items, isDesignation = false) {
        dropdown.innerHTML = ""; // Clear previous items
        items.slice(0, 5).forEach(item => { // Show only top 5
            const li = document.createElement("li");
            li.className = "dropdown-item";
            li.textContent = item;
            li.style.cursor = "pointer";
            li.onclick = function () {
                if (isDesignation) {
                    updateDesignation(item);
                } else {
                    input.value = item; // Set office input value
                }
                dropdown.innerHTML = ""; // Close dropdown
            };
            dropdown.appendChild(li);
        });
    }

    function filterOptions(query, items) {
        return items.filter(item => item.includes(query)).slice(0, 5);
    }

    // Function to update designation input with (ভা:) if selected
    function updateDesignation(selectedDesignation) {
        const actingCheck = document.getElementById("actingCheck");
        const designationInput = document.getElementById("designation");

        if (actingCheck.checked) {
            designationInput.value = selectedDesignation + " (ভা:)";
        } else {
            designationInput.value = selectedDesignation;
        }
    }

    // Handle radio button toggle for (ভা:)
    document.getElementById("actingCheck").addEventListener("change", function () {
        const designationInput = document.getElementById("designation");
        if (this.checked && designationInput.value) {
            designationInput.value = designationInput.value.replace(" (ভা:)", "") + " (ভা:)";
        } else {
            designationInput.value = designationInput.value.replace(" (ভা:)", "");
        }
    });

    // Designation dropdown logic
    const designationInput = document.getElementById("designation");
    const designationDropdown = document.getElementById("designationDropdown");

    designationInput.addEventListener("focus", function () {
        populateDropdown(designationInput, designationDropdown, designations, true);
    });

    designationInput.addEventListener("input", function () {
        const filtered = filterOptions(this.value, designations);
        populateDropdown(designationInput, designationDropdown, filtered, true);
    });

    // Office dropdown logic
    const officeInput = document.getElementById("office");
    const officeDropdown = document.getElementById("officeDropdown");

    officeInput.addEventListener("focus", function () {
        populateDropdown(officeInput, officeDropdown, offices, false);
    });

    officeInput.addEventListener("input", function () {
        const filtered = filterOptions(this.value, offices);
        populateDropdown(officeInput, officeDropdown, filtered, false);
    });

    document.addEventListener("click", function (event) {
        if (!designationInput.contains(event.target) && !designationDropdown.contains(event.target)) {
            designationDropdown.innerHTML = ""; // Hide dropdown when clicking outside
        }
        if (!officeInput.contains(event.target) && !officeDropdown.contains(event.target)) {
            officeDropdown.innerHTML = ""; // Hide dropdown when clicking outside
        }
    });

    // Initialize with top 5 options
    populateDropdown(designationInput, designationDropdown, designations, true);
    populateDropdown(officeInput, officeDropdown, offices, false);
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const officeInput = document.getElementById("officeInput");
    const officeDropdown = document.getElementById("officeDropdown");

    function filterOffices(searchText) {
        let filteredOffices = offices.filter(office => office.includes(searchText));
        return searchText ? filteredOffices : offices.slice(0, 5);
    }

    function updateOfficeDropdown(filteredOffices) {
        officeDropdown.innerHTML = '';
        filteredOffices.forEach(office => {
            const li = document.createElement("li");
            li.className = "dropdown-item";
            li.textContent = office;
            li.style.cursor = "pointer";
            li.onclick = function () {
                officeInput.value = office;
                officeDropdown.innerHTML = ""; // Close dropdown
            };
            officeDropdown.appendChild(li);
        });
    }

    officeInput.addEventListener("focus", function () {
        updateOfficeDropdown(filterOffices(""));
    });

    officeInput.addEventListener("input", function () {
        updateOfficeDropdown(filterOffices(this.value));
    });

    document.addEventListener("click", function (event) {
        if (!officeInput.contains(event.target) && !officeDropdown.contains(event.target)) {
            officeDropdown.innerHTML = ""; // Hide dropdown when clicking outside
        }
    });
});
</script>


<script>
$(document).ready(function(){
    $("#searchId").click(function(){
        let Official_ID = $("#Official_ID_1").val().trim();

        // Check if ID is entered
        if (Official_ID === "") {
            Toastify({
                text: "Please enter an ID number",
                duration: 3000,
                gravity: "top", 
                position: "right", 
                backgroundColor: "red",
            }).showToast();
            return;
        }

        console.log("Searching for ID:", Official_ID_1); // Debugging

        // AJAX request to check if ID exists
        $.ajax({
            url: "add_search_participant.php",  // PHP script to handle search
            method: "POST",
            data: { Official_ID: Official_ID },
            dataType: "json",
            success: function(response) {
                console.log("AJAX Response:", response); // Debugging

                if (response.exists) {
                    // Auto-fill input fields if participant exists
                    $("#name").val(response.name);
                    $("#designation").val(response.designation);
                    $("#office").val(response.office_address); 
                    $("#contact").val(response.contact);
                    $("#email").val(response.email);
					$("#gender").val(response.gender);
					$("#Official_ID").val(response.Official_ID);

                    Toastify({
                        text: "Participant found!",
                        duration: 3000,
                        gravity: "top", 
                        position: "right", 
                        backgroundColor: "green",
                    }).showToast();
                } else {
                    // Show notification if ID not found
                    Toastify({
                        text: "This person is not registered yet",
                        duration: 3000,
                        gravity: "top", 
                        position: "right", 
                        backgroundColor: "orange",
                    }).showToast();
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
                Toastify({
                    text: "Server error! Please try again later.",
                    duration: 3000,
                    gravity: "top", 
                    position: "right", 
                    backgroundColor: "red",
                }).showToast();
            }
        });
    });
});

</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


