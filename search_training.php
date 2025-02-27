<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Training</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?> <!-- Include the navbar -->

<div class="container my-5 text-center">
    <h2>Search Training Records</h2>
    <div class="row justify-content-center mt-4">
        <div class="col-md-5">
            <a href="search_by_name.php" class="btn btn-primary btn-lg w-100">Search by Participant Name</a>
        </div>
        <div class="col-md-5">
            <a href="search_by_training.php" class="btn btn-secondary btn-lg w-100 btn-warning">Search by Training Name</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
