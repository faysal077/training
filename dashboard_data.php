<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include 'db_connection.php';

header('Content-Type: application/json');

// Function to get the current fiscal year
function getCurrentFiscalYear() {
    $currentMonth = date('m');
    $currentYear = date('Y');
    return ($currentMonth >= 7) ? "$currentYear-" . ($currentYear + 1) : ($currentYear - 1) . "-$currentYear";
}

$fiscalYear = getCurrentFiscalYear();

// Ensure output is only JSON
ob_clean(); // Clear any previous output

$response = [
    "current_year_trainings" => 0,
    "total_participants" => 0,
    "total_expenses" => 0,
    "total_trainings" => 0
];

try {
    // Fetch total trainings in the current fiscal year
    $sql1 = "SELECT COUNT(*) AS current_year_trainings FROM batches WHERE fiscal_year = ?";
    $stmt1 = $conn->prepare($sql1);
    if ($stmt1) {
        $stmt1->bind_param("s", $fiscalYear);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        if ($row = $result1->fetch_assoc()) {
            $response["current_year_trainings"] = $row["current_year_trainings"];
        }
        $stmt1->close();
    }

    // Fetch total participants
    $sql2 = "SELECT COUNT(*) AS total_participants FROM participants";
    if ($result2 = $conn->query($sql2)) {
        if ($row2 = $result2->fetch_assoc()) {
            $response["total_participants"] = $row2["total_participants"];
        }
    }

    // Fetch total financial expenses
    $sql3 = "SELECT SUM(amount_spent) AS total_expenses FROM financial_clearance";
    if ($result3 = $conn->query($sql3)) {
        if ($row3 = $result3->fetch_assoc()) {
            $response["total_expenses"] = $row3["total_expenses"] ?? 0;
        }
    }

    // Fetch total number of trainings
    $sql4 = "SELECT COUNT(*) AS total_trainings FROM trainings";
    if ($result4 = $conn->query($sql4)) {
        if ($row4 = $result4->fetch_assoc()) {
            $response["total_trainings"] = $row4["total_trainings"];
        }
    }
	// Fetch participant data grouped by fiscal year
	$participantData = [];

	$sql5 = "SELECT 
				b.fiscal_year, 
				COUNT(p.id) AS total_participants, 
				SUM(CASE WHEN p.gender = 'Male' THEN 1 ELSE 0 END) AS male_participants,
				SUM(CASE WHEN p.gender = 'Female' THEN 1 ELSE 0 END) AS female_participants
			FROM participants p
			JOIN batches b ON p.batch_id = b.id
			GROUP BY b.fiscal_year
			ORDER BY b.fiscal_year ASC";

	if ($result5 = $conn->query($sql5)) {
		while ($row5 = $result5->fetch_assoc()) {
			$participantData[] = [
				"fiscal_year" => $row5["fiscal_year"],
				"total_participants" => $row5["total_participants"],
				"male_participants" => $row5["male_participants"],
				"female_participants" => $row5["female_participants"]
			];
		}
	}
	// Fetch financial activities data grouped by fiscal year
	$financialData = [];

	$sql6 = "SELECT 
				b.fiscal_year, 
				SUM(fc.amount_spent) AS total_spent
			 FROM financial_clearance fc
			 JOIN batches b ON fc.batch_id = b.id
			 GROUP BY b.fiscal_year
			 ORDER BY b.fiscal_year ASC";

	if ($result6 = $conn->query($sql6)) {
		while ($row6 = $result6->fetch_assoc()) {
			$financialData[] = [
				"fiscal_year" => $row6["fiscal_year"],
				"total_spent" => (float) $row6["total_spent"]
			];
		}
	}

// Add financial data to response
$response["financial_chart_data"] = $financialData;


// Add participant data to response
$response["participant_chart_data"] = $participantData;


    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
