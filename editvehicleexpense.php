<?php
include("db.php");
date_default_timezone_set('Asia/Kolkata');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Vehicle Expense List</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; }
        h2 { text-align: center; color: #333; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .edit-btn { background: #2196F3; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        .edit-btn:hover { background: #0b7dda; }
        .search-container { text-align: center; margin-bottom: 20px; }
        input[type="text"] { padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 8px 12px; border: none; background: green; color: white; border-radius: 4px; cursor: pointer; }
        button:hover { background: darkgreen; }
    </style>
</head>
<body>

<h2>Vehicle Expense Records (Edit List)</h2>

<div class="search-container">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by Chasis No..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Search</button>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Chasis No</th>
        <th>RTO Amount (₹)</th>
        <th>RTO Date</th>
        <th>Insurance Amount (₹)</th>
        <th>Insurance Date</th>
        <th>Action</th>
    </tr>

<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search != '') {
    $stmt = $conn->prepare("SELECT id, chasis_no, rto_amount, rto_date, insurance_amount, insurance_date FROM vehicle_expense WHERE chasis_no LIKE ?");
    $searchParam = "%$search%";
    $stmt->bind_param("s", $searchParam);
} else {
    $stmt = $conn->prepare("SELECT id, chasis_no, rto_amount, rto_date, insurance_amount, insurance_date FROM vehicle_expense ORDER BY id DESC");
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['chasis_no']}</td>
                <td>₹{$row['rto_amount']}</td>
                <td>{$row['rto_date']}</td>
                <td>₹{$row['insurance_amount']}</td>
                <td>{$row['insurance_date']}</td>
                <td><a class='edit-btn' href='edittvehicelexpense.php?id={$row['id']}'>Edit</a></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No records found</td></tr>";
}

$stmt->close();
$conn->close();
?>
</table>

</body>
</html>
