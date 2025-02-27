<?php
include 'db_connection.php';

$name = "";
$trainings = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    
    if (!empty($name)) {
        $query = "SELECT t.title, b.start_date, b.end_date, b.batch_number AS batch_number
                  FROM trainings t
                  JOIN participants p ON t.id = p.training_id
                  JOIN batches b ON p.batch_id = b.id
                  WHERE p.name LIKE '%$name%'
                  ORDER BY b.start_date DESC";
        
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $trainings[] = $row;
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
    <title>Search by Participant Name</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container my-4">
    <h2 class="text-center">Search Training by Participant Name</h2>
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="Enter Participant Name" required value="<?php echo htmlspecialchars($name); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if (!empty($trainings)): ?>
        <h4>Training History for: <strong><?php echo htmlspecialchars($name); ?></strong></h4>
        <table class="table table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Training Name</th>
                    <th>Training Start Date</th>
                    <th>Training End Date</th>
                    <th>Batch No</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainings as $training): ?>
                    <tr>
                        <td><?php echo $training['title']; ?></td>
                        <td><?php echo $training['start_date']; ?></td>
                        <td><?php echo $training['end_date']; ?></td>
                        <td><?php echo $training['batch_number']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p class="text-danger">No training records found for "<strong><?php echo htmlspecialchars($name); ?></strong>".</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
