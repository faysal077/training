<?php
include 'db_connection.php';

$training_id = $_GET['training_id'];
$batch_id = $_GET['batch_id'];

// Fetch participants for the specific training and batch
$query = "SELECT * FROM participants WHERE training_id = '$training_id' AND batch_id = '$batch_id' ORDER BY batch_number";
$result = mysqli_query($conn, $query);

// Fetch batch_number for the given batch_id
$batch_query = "SELECT batch_number FROM batches WHERE id = '$batch_id'";
$batch_result = mysqli_query($conn, $batch_query);
$batch_row = mysqli_fetch_assoc($batch_result);
$batch_number = $batch_row['batch_number'] ?? '';

// Function to convert numbers to Bangla
function convertToBanglaNumber($number) {
    $english_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bangla_numbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return str_replace($english_numbers, $bangla_numbers, $number);
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অংশগ্রহণকারীদের তালিকা</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function confirmDelete(participantId) {
            if (confirm("আপনি কি নিশ্চিত যে আপনি এই অংশগ্রহণকারীকে মুছে ফেলতে চান?")) {
                window.location.href = "delete_participant.php?id=" + participantId;
            }
        }
    </script>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container my-4">
    <h3>অংশগ্রহণকারীদের তালিকা | প্রশিক্ষণ আইডি: <?php echo convertToBanglaNumber($training_id); ?> | ব্যাচ আইডি: <?php echo convertToBanglaNumber($batch_id); ?></h3>

    <a href="add_participant.php?training_id=<?php echo $training_id; ?>&batch_id=<?php echo $batch_id; ?>&batch_number=<?php echo urlencode($batch_number); ?>" class="btn btn-success float-end">
        নতুন অংশগ্রহণকারী যোগ করুন
    </a>
    <a href="generate_participant_word.php?training_id=<?php echo $training_id; ?>&batch_id=<?php echo $batch_id; ?>" class="btn btn-primary mb-3">
        অংশগ্রহণকারী তালিকা ডাউনলোড করুন
    </a>

    <h4>ব্যাচের অংশগ্রহণকারীরা</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ক্রমিক</th>
                <th>নাম</th>
                <th>পদবি</th>
                <th>কার্যালয়</th>
                <th>মোবাইল</th>
                <th>ইমেইল</th>
                <th>মুছুন</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $serial = 1;
            while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo convertToBanglaNumber($serial++); ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['designation']; ?></td>
                    <td><?php echo $row['office_address']; ?></td>
                    <td><?php echo $row['contact']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['id']; ?>)">
                            ❌
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
