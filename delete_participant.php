<?php
include 'db_connection.php';

if (!isset($_GET['id'])) {
    die("অবৈধ অনুরোধ।");
}

$participant_id = intval($_GET['id']);

$query = "DELETE FROM participants WHERE id = '$participant_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    echo "ডিলেট করতে ব্যর্থ হয়েছে: " . mysqli_error($conn);
}
?>
