<?php
include 'db_connection.php';

$training_name = "";
$participants = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_name = trim($_POST['training_name']);
    
    if (!empty($training_name)) {
        $query = "SELECT p.name, p.designation, b.start_date, b.end_date, b.batch_number AS batch_number
                  FROM participants p
                  JOIN trainings t ON p.training_id = t.id
                  JOIN batches b ON p.batch_id = b.id
                  WHERE t.title LIKE '%$training_name%'
                  ORDER BY b.start_date DESC";
        
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $participants[] = $row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search by Training Name</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container my-4">
    <h2 class="text-center">Search Participants by Training Name</h2>
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="training_name" class="form-control" placeholder="Enter Training Name" required value="<?php echo htmlspecialchars($training_name); ?>">
            <button type="submit" class="btn btn-secondary">Search</button>
        </div>
    </form>

    <?php if (!empty($participants)): ?>
        <h4>Participant List for Training: <strong><?php echo htmlspecialchars($training_name); ?></strong></h4>
        <table class="table table-bordered">
            <thead class="table-success">
                <tr>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Training Start Date</th>
                    <th>Training End Date</th>
                    <th>Batch Number</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $participant): ?>
                    <tr>
                        <td><?php echo $participant['name']; ?></td>
                        <td><?php echo $participant['designation']; ?></td>
                        <td><?php echo $participant['start_date']; ?></td>
                        <td><?php echo $participant['end_date']; ?></td>
                        <td><?php echo $participant['batch_number']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p class="text-danger">No participants found for training "<strong><?php echo htmlspecialchars($training_name); ?></strong>".</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
