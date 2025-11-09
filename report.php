<?php
include("db.php");
date_default_timezone_set('Asia/Kolkata');

/*-------------------------------------------------
 🧾 1️⃣ Fetch Monthly Delivery Count (Followup Table)
-------------------------------------------------*/
$delivery_query = "
    SELECT 
        DATE_FORMAT(followup_date, '%Y-%m') AS month,
        COUNT(*) AS delivery_count
    FROM followup
    WHERE followup_date IS NOT NULL
    GROUP BY month
    ORDER BY month ASC
";
$delivery_result = $conn->query($delivery_query);

if (!$delivery_result) {
    die("Delivery Query Failed: " . $conn->error);
}

$months = [];
$delivery_counts = [];
while ($row = $delivery_result->fetch_assoc()) {
    $months[] = date("M Y", strtotime($row['month'] . "-01"));
    $delivery_counts[] = (int)$row['delivery_count'];
}

/*-------------------------------------------------
 💸 2️⃣ Fetch Monthly Expense Total (VehicleExpense Table)
-------------------------------------------------*/
$expense_query = "
    SELECT 
        DATE_FORMAT(vehicle_incoming_date, '%Y-%m') AS month,
        SUM(rto_amount + insurance_amount + total_expense) AS total_expense
    FROM vehicle_expense
    WHERE vehicle_incoming_date IS NOT NULL
    GROUP BY month
    ORDER BY month ASC
";
$expense_result = $conn->query($expense_query);

if (!$expense_result) {
    die("Expense Query Failed: " . $conn->error);
}

$expense_months = [];
$expenses = [];
while ($row = $expense_result->fetch_assoc()) {
    $expense_months[] = date("M Y", strtotime($row['month'] . "-01"));
    $expenses[] = (float)$row['total_expense'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>BMMS Motors - Sales & Expense Report</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body {
        font-family: "Segoe UI", Tahoma, sans-serif;
        background: #f5f7fa;
        padding: 25px;
        color: #333;
    }
    h2 {
        text-align: center;
        color: #222;
        margin-bottom: 30px;
        font-size: 28px;
    }
    .chart-container {
        width: 90%;
        max-width: 1000px;
        margin: 40px auto;
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    canvas {
        width: 100% !important;
        height: 400px !important;
    }
</style>
</head>
<body>

<h2>📊 BMMS Motors - Monthly Delivery & Expense Report</h2>

<!-- Bar Chart for Monthly Deliveries -->
<div class="chart-container">
    <h3 style="text-align:center;">Customer Deliveries - Month Wise</h3>
    <canvas id="deliveryChart"></canvas>
</div>

<!-- Pie Chart for Monthly Expenses -->
<div class="chart-container">
    <h3 style="text-align:center;">Expense Distribution - Month Wise</h3>
    <canvas id="expenseChart"></canvas>
</div>

<script>
// 🟦 Bar Chart - Delivery Count
const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
new Chart(deliveryCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Deliveries',
            data: <?php echo json_encode($delivery_counts); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Number of Deliveries per Month',
                font: { size: 18 }
            },
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Customers Delivered' }
            },
            x: {
                title: { display: true, text: 'Month' }
            }
        }
    }
});

// 🟠 Pie Chart - Monthly Expense
const expenseCtx = document.getElementById('expenseChart').getContext('2d');
new Chart(expenseCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($expense_months); ?>,
        datasets: [{
            label: 'Total Expense (₹)',
            data: <?php echo json_encode($expenses); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(255, 205, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(201, 203, 207, 0.8)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Month-wise Expense Distribution',
                font: { size: 18 }
            },
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

</body>
</html>
