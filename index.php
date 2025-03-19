<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
<?php include 'navbar.php'; ?> <!-- Include the navbar -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-info p-3 text-white text-center">
                    <h3 id="current-year-trainings">Loading...</h3>
                    <p>Trainings This Fiscal Year</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-success p-3 text-white text-center">
                    <h3 id="total-participants">Loading...</h3>
                    <p>Total Participants</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-primary p-3 text-white text-center">
                    <h3 id="total-participants-this-year">Loading...</h3>
                    <p>Total Participants This Fiscal Year</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-warning p-3 text-white text-center">
                    <h3 id="total-expenses">Loading...</h3>
                    <p>Total Financial Expenses</p>
                </div>
            </div>
			<div class="col-lg-3 col-md-6">
					<div class="small-box bg-dark p-3 text-white text-center">
						<h3 id="total-expenses-this-year">Loading...</h3>
						<p>Total Financial Expenses (This Fiscal Year)</p>
					</div>
				</div>
            <div class="col-lg-3 col-md-6">
                <div class="small-box bg-danger p-3 text-white text-center">
                    <h3 id="total-trainings">Loading...</h3>
                    <p>Total Trainings</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Participant Statistics</h5>
                    <div id="participantChart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Financial Activities</h5>
                    <div id="financialChart"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $.ajax({
            url: 'dashboard_data.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log("AJAX Success Response: ", response);

                if (response.error) {
                    console.error("Error from server:", response.error);
                    alert("Server Error: " + response.error);
                    return;
                }

                // Update text data
                $('#current-year-trainings').text(response.current_year_trainings || 0);
                $('#total-participants').text(response.total_participants || 0);
                $('#total-participants-this-year').text(response.total_participants_this_year || 0);
                $('#total-expenses').text(response.total_expenses || 0);
				 $('#total-expenses-this-year').text(response.total_expenses_this_year || 0); // Updated
                $('#total-trainings').text(response.total_trainings || 0);

                // Process participant chart data
                let years = [];
                let totalParticipants = [];
                let maleParticipants = [];
                let femaleParticipants = [];

                response.participant_chart_data.forEach(function(item) {
                    years.push(item.fiscal_year);
                    totalParticipants.push(item.total_participants);
                    maleParticipants.push(item.male_participants);
                    femaleParticipants.push(item.female_participants);
                });

                var participantChart = new ApexCharts(document.querySelector("#participantChart"), {
                    chart: { type: 'line', height: 350 },
                    series: [
                        { name: "Total Participants", data: totalParticipants },
                        { name: "Male Participants", data: maleParticipants },
                        { name: "Female Participants", data: femaleParticipants }
                    ],
                    xaxis: { categories: years, title: { text: "Fiscal Year" } },
                    yaxis: { title: { text: "Number of Participants" } },
                    colors: ['#008FFB', '#00E396', '#FF4560']
                });
                participantChart.render();

                // Process financial chart data
                let financialYears = [];
                let totalSpent = [];

                response.financial_chart_data.forEach(function(item) {
                    financialYears.push(item.fiscal_year);
                    totalSpent.push(item.total_spent);
                });

                var financialChart = new ApexCharts(document.querySelector("#financialChart"), {
                    chart: { type: 'line', height: 350 },
                    series: [{ name: "Total Amount Spent", data: totalSpent }],
                    xaxis: { categories: financialYears, title: { text: "Fiscal Year" } },
                    yaxis: { title: { text: "Amount Spent" } },
                    colors: ['#FF9800']
                });
                financialChart.render();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                alert("Failed to load data: " + error);
            }
        });
    });
    </script>
</body>
</html>
