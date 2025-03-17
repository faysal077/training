<div class="sidebar">
    <a href="search_training.php">🏠 Home</a>
    <a href="#" onclick="loadPage('search_by_name.php'); return false;">🔍 Search by Name</a>
	<a href="#" onclick="loadPage('search_by_training.php'); return false;">📚 Search by Training</a>
	<a href="#" onclick="loadPage('search_not_taken.php'); return false;">🚫 Unattended Trainings</a>
	<a href="#" onclick="loadPage('search_by_multiple_trainings.php'); return false;">🔍 Search by Multiple Trainings</a>
	
</div>

<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }
    .sidebar {
        height: 80vh;
        width: 250px;
        position: fixed;
        top: 10vh;
        left: 0;
        background-color: #343a40;
        padding-top: 20px;
        border-radius: 0 10px 10px 0;
    }
    .sidebar a {
        padding: 15px 20px;
        display: block;
        font-size: 18px;
        color: white;
        text-decoration: none;
        transition: 0.3s;
    }
    .sidebar a:hover {
        background-color: #495057;
    }
</style>
