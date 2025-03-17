<?php
include 'db_connection.php';

$training_names = "";
$participants = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $training_names = trim($_POST['training_names']);
    $training_list = array_map('trim', explode(',', $training_names)); // Convert input to an array

    if (!empty($training_list)) {
        // Construct the SQL query dynamically
        $placeholders = implode(',', array_fill(0, count($training_list), '?'));
        $training_query = "SELECT id FROM trainings WHERE title IN ($placeholders)";

        $stmt = mysqli_prepare($conn, $training_query);
        if ($stmt) {
            $types = str_repeat('s', count($training_list));
            mysqli_stmt_bind_param($stmt, $types, ...$training_list);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $training_ids = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $training_ids[] = $row['id'];
            }
            mysqli_stmt_close($stmt);

            if (!empty($training_ids)) {
                // Fetch participants for the selected trainings
                $id_placeholders = implode(',', array_fill(0, count($training_ids), '?'));
                $participant_query = "SELECT p.name, p.designation, p.batch_number, t.title AS training_name
                                      FROM participants p
                                      JOIN trainings t ON p.training_id = t.id
                                      WHERE p.training_id IN ($id_placeholders)";

                $stmt = mysqli_prepare($conn, $participant_query);
                if ($stmt) {
                    $types = str_repeat('i', count($training_ids));
                    mysqli_stmt_bind_param($stmt, $types, ...$training_ids);
                    mysqli_stmt_execute($stmt);
                    $participants = mysqli_stmt_get_result($stmt);
                }
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
    <title>Search by Training Names</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container my-4">
    <h2 class="text-center">Search Participants by Multiple Training Names</h2>
    <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class="mb-4">
        <div class="input-group">
            <input type="text" name="training_names" class="form-control" placeholder="Enter Training Names (comma-separated)" required value="<?= htmlspecialchars($training_names); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <?php if (!empty($participants) && mysqli_num_rows($participants) > 0): ?>
            <h4>Search Results for: <strong><?= htmlspecialchars($training_names); ?></strong></h4>
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Batch Number</th>
                        <th>Training Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($participants)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['designation']) ?></td>
                            <td><?= htmlspecialchars($row['batch_number']) ?></td>
                            <td><?= htmlspecialchars($row['training_name']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-danger">No participants found for "<strong><?= htmlspecialchars($training_names); ?></strong>".</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
