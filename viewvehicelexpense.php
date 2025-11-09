<?php
include("db.php");
date_default_timezone_set('Asia/Kolkata');

// Handle search
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM vehicle_expense WHERE chasis_no LIKE ? ORDER BY created_at DESC");
    $like = "%" . $search . "%";
    $stmt->bind_param("s", $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM vehicle_expense ORDER BY created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Vehicle Expense - BMMS Motors</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f4f6f8;
            padding: 20px;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 20px;
        }
        form {
            text-align: center;
            margin-bottom: 25px;
        }
        input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 250px;
        }
        button {
            padding: 8px 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th, td {
            border-bottom: 1px solid #e5e5e5;
            padding: 10px 15px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .extra-table {
            border: 1px solid #ccc;
            width: 90%;
            margin-top: 5px;
            border-collapse: collapse;
        }
        .extra-table th, .extra-table td {
            border: 1px solid #ccc;
            padding: 4px 8px;
            font-size: 13px;
        }
        .print-btn {
            background: #28a745;
            margin-bottom: 20px;
        }
        .print-btn:hover {
            background: #1e7e34;
        }
    </style>
</head>
<body>

<h2>Vehicle Expense Report</h2>

<form method="GET">
    <input type="text" name="search" placeholder="Search by Chasis No" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
    <button type="button" class="print-btn" onclick="window.print()">🖨 Print Report</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Chasis No</th>
        <th>RTO Amount</th>
        <th>RTO Date</th>
        <th>Insurance Amount</th>
        <th>Insurance Date</th>
        <th>Incoming Date</th>
        <th>Extra Expenses</th>
        <th>Total Expense</th>
        <th>Created At</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($row['chasis_no']); ?></strong></td>
                <td>₹<?php echo number_format($row['rto_amount'], 2); ?></td>
                <td><?php echo $row['rto_date']; ?></td>
                <td>₹<?php echo number_format($row['insurance_amount'], 2); ?></td>
                <td><?php echo $row['insurance_date']; ?></td>
                <td><?php echo $row['vehicle_incoming_date']; ?></td>
                <td>
                    <?php
                    $extras = json_decode($row['extra_expenses'], true);
                    if (!empty($extras)) {
                        echo "<table class='extra-table'><tr><th>Description</th><th>Amount</th></tr>";
                        foreach ($extras as $ex) {
                            echo "<tr><td>" . htmlspecialchars($ex['desc']) . "</td><td>₹" . number_format($ex['amount'], 2) . "</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<em>No Extra Expenses</em>";
                    }
                    ?>
                </td>
                <td><strong>₹<?php echo number_format($row['total_expense'], 2); ?></strong></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="10" style="text-align:center;">No records found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
