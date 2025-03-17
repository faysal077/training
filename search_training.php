<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Management Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="content" style="margin-left: 260px; padding: 20px;">
    <h2>Training Management Dashboard</h2>
    <div id="main-content">
        <p>Select an option from the left menu to get started.</p>
    </div>
</div>

<script>
    function loadPage(page) {
        $.ajax({
            url: page,
            type: "GET",
            success: function(response) {
                $("#main-content").html(response);
                window.history.pushState({ page: page }, "", "?page=" + page); // Maintain browser history
            },
            error: function() {
                $("#main-content").html("<p class='text-danger'>Error loading page.</p>");
            }
        });
    }

    // Handle search form submission dynamically (only inside #main-content)
    $(document).on("submit", "#main-content form", function(event) {
        event.preventDefault(); // Prevent page reload
        var form = $(this);
        
        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                $("#main-content").html(response);
            },
            error: function() {
                $("#main-content").html("<p class='text-danger'>Error processing request.</p>");
            }
        });
    });

    // Handle browser Back/Forward button navigation
    window.onpopstate = function(event) {
        if (event.state && event.state.page) {
            loadPage(event.state.page);
        }
    };

    // Load the correct page if accessed via URL (e.g., ?page=search_by_name.php)
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get("page");
        if (page) {
            loadPage(page);
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
