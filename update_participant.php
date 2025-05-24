<?php
include 'db_connection.php';

if (!isset($_GET['id'])) {
    die("অবৈধ অনুরোধ।");
}

$participant_id = intval($_GET['id']);

echo $participant_id. " ";

// Fetch participant data
$query = "SELECT * FROM participants WHERE id = '$participant_id'";
$result = mysqli_query($conn, $query);
$participant = mysqli_fetch_assoc($result);

echo $participant['training_id']." ".$participant['batch_id'];

if (!$participant) {
    die("অংশগ্রহণকারী খুঁজে পাওয়া যায়নি।");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $Official_ID = $_POST['Official_ID'];
    $designation = $_POST['designation'];
    $office_address = $_POST['office_address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    $update_query = "UPDATE participants SET 
        name = '$name',
        Official_ID = '$Official_ID',
        designation = '$designation',
        office_address = '$office_address',
        contact = '$contact',
        email = '$email'
        WHERE id = '$participant_id'";

    if (mysqli_query($conn, $update_query)) {
        header("Location: participants_list.php?training_id={$participant['training_id']}&batch_id={$participant['batch_id']}");
        exit;
    } else {
        echo "আপডেট করতে ব্যর্থ হয়েছে: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>অংশগ্রহণকারী সম্পাদনা</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>অংশগ্রহণকারী তথ্য সম্পাদনা করুন</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">নাম</label>
            <input type="text" name="name" class="form-control" value="<?php echo $participant['name']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">নথি নম্বর</label>
            <input type="text" name="Official_ID" class="form-control" value="<?php echo $participant['Official_ID']; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">পদবি</label>
            <input type="text" name="designation" class="form-control" value="<?php echo $participant['designation']; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">কার্যালয়</label>
            <input type="text" name="office_address" class="form-control" value="<?php echo $participant['office_address']; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">মোবাইল</label>
            <input type="text" name="contact" class="form-control" value="<?php echo $participant['contact']; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">ইমেইল</label>
            <input type="email" name="email" class="form-control" value="<?php echo $participant['email']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
        <a href="participant_list.php?training_id=<?php echo $participant['training_id']; ?>&batch_id=<?php echo $participant['batch_id']; ?>" class="btn btn-secondary">বাতিল</a>
    </form>
</div>
</body>
</html>
