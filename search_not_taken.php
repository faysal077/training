<?php
include 'db_connection.php';

$name = "";
$unattended_trainings = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    
    if (!empty($name)) {
        // Fix: Use Prepared Statements for Security
        $query = "
            SELECT t.title, t.start_date, t.end_date, t.fiscal_year
            FROM trainings t
            WHERE t.id NOT IN (
                SELECT p.training_id 
                FROM participants p
                WHERE p.name LIKE ?
            )
            ORDER BY t.start_date DESC";
        
        $stmt = mysqli_prepare($conn, $query);
        $search_param = "%$name%";
        mysqli_stmt_bind_param($stmt, "s", $search_param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $unattended_trainings[] = $row;
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
    <title>Search Unattended Trainings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container my-4">
    <h2 class="text-center">Search Trainings Not Taken by Participant</h2>

    <!-- Fix: Ensure form action points to the same page -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-4">
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="Enter Participant Name" required 
                value="<?php echo htmlspecialchars($name); ?>">
            <button type="submit" class="btn btn-danger">Search</button>
        </div>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($name)): ?>
        <h4>Trainings NOT attended by: <strong><?php echo htmlspecialchars($name); ?></strong></h4>
        
        <?php if (!empty($unattended_trainings)): ?>
            <table class="table table-bordered">
                <thead class="table-danger">
                    <tr>
                        <th>Training Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Fiscal Year</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unattended_trainings as $training): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($training['title']); ?></td>
                            <td><?php echo htmlspecialchars($training['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($training['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($training['fiscal_year']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-danger">No unattended trainings found for "<strong><?php echo htmlspecialchars($name); ?></strong>".</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
