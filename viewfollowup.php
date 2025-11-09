<?php
include("db.php");
date_default_timezone_set('Asia/Kolkata');

$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$sql = "SELECT * FROM followup WHERE customer_name LIKE ? OR customer_phone LIKE ? ORDER BY followup_date DESC";
$stmt = $conn->prepare($sql);
$searchParam = "%" . $search . "%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

$total_onroad = 0;
$total_base = 0;
$total_remaining = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Follow-Up Records</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 30px; }
        h2 { text-align: center; color: #333; }
        .search-bar { text-align: center; margin-bottom: 20px; }
        input[type="text"] {
            padding: 8px; width: 300px; border: 1px solid #ccc;
            border-radius: 5px; font-size: 14px;
        }
        button {
            padding: 8px 12px; background: green; color: white;
            border: none; border-radius: 5px; cursor: pointer;
        }
        button:hover { background: darkgreen; }
        table {
            width: 100%; border-collapse: collapse; background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd; padding: 10px; text-align: center;
        }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .totals {
            background: #333; color: #fff; font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Follow-Up Report</h2>

<div class="search-bar">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by Name or Phone" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <button type="button" onclick="window.location.href='viewfollowup.php'">Reset</button>
    </form>
</div>

<table>
    <tr>
        <th>#</th>
        <th>Customer Name</th>
        <th>Phone</th>
        <th>Vehicle Type</th>
        <th>Range</th>
        <th>On-road Amount (₹)</th>
        <th>Total Base Payment (₹)</th>
        <th>Remaining (₹)</th>
        <th>Remaining Payment Date</th>
        <th>Payment Type</th>
        <th>Bank</th>
        <th>Location</th>
        <th>Follow-up Date</th>
    </tr>

    <?php
    $i = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$i}</td>
            <td>{$row['customer_name']}</td>
            <td>{$row['customer_phone']}</td>
            <td>{$row['vehicle_type']}</td>
            <td>{$row['vehicle_range']}</td>
            <td>{$row['onroad_amount']}</td>
            <td>{$row['total_base_payment']}</td>
            <td>{$row['remaining_amount']}</td>
            <td>{$row['remaining_payment_date']}</td>
            <td>{$row['payment_type']}</td>
            <td>{$row['bank_name']}</td>
            <td>{$row['bank_location']}</td>
            <td>{$row['followup_date']}</td>
        </tr>";

        $total_onroad += $row['onroad_amount'];
        $total_base += $row['total_base_payment'];
        $total_remaining += $row['remaining_amount'];
        $i++;
    }
    ?>
    <tr class="totals">
        <td colspan="5">Total</td>
        <td>₹<?= number_format($total_onroad, 2) ?></td>
        <td>₹<?= number_format($total_base, 2) ?></td>
        <td>₹<?= number_format($total_remaining, 2) ?></td>
        <td colspan="5"></td>
    </tr>
</table>

</body>
</html>
