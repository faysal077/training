<?php
header('Content-Type: application/json'); // Ensure JSON output
include 'db_connection.php'; // Ensure your database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Official_ID'])) {
    $official_id = trim($_POST['Official_ID']); // Trim whitespace just in case

    $stmt = $conn->prepare("SELECT name, designation, office_address, contact, email, gender, Official_ID FROM participants WHERE Official_ID = ?");
    $stmt->bind_param("s", $official_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "exists" => true,
            "name" => $row["name"] ?? "",
            "designation" => $row["designation"] ?? "",
            "office_address" => $row["office_address"] ?? "",
            "contact" => $row["contact"] ?? "",
            "email" => $row["email"] ?? "",
			"gender" => $row["gender"] ?? "",
			"Official_ID" => $row["Official_ID"] ?? ""
        ]);
    } else {
        echo json_encode(["exists" => false]);
    }

    $stmt->close();
    $conn->close();
}
