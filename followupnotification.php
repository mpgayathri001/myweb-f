<?php
include 'db.php';
date_default_timezone_set('Asia/Kolkata');

$today = date('Y-m-d');

// ✅ Handle update request (change next payment date)
if (isset($_POST['update_date'])) {
    $id = intval($_POST['id']);
    $new_date = $_POST['new_date'];
    $updateQuery = "UPDATE followup SET remaining_payment_date='$new_date' WHERE id=$id";
    mysqli_query($conn, $updateQuery);
    echo "<script>alert('Payment reminder updated successfully!');window.location.href='admindashboard.php';</script>";
    exit();
}

// ✅ Fetch only today’s reminders
$query = "SELECT * FROM followup 
          WHERE remaining_payment_date = '$today' 
          ORDER BY remaining_payment_date ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Today's Payment Reminders | PROBIS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #1e3a8a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #1e3a8a;
            color: white;
        }
        tr:nth-child(even) { background-color: #f2f2f2; }

        .due-today { background-color: #fff3cd; color: #856404; }

        .btn {
            padding: 6px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .update-btn {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Today's Payment Reminders</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Phone</th>
            <th>Vehicle Type</th>
            <th>Remaining Amount</th>
            <th>Payment Type</th>
            <th>Remaining Payment Date</th>
            <th>Total Base Payment</th>
            <th>Action</th>
        </tr>

        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <tr class="due-today">
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['customer_phone']; ?></td>
                <td><?php echo $row['vehicle_type']; ?></td>
                <td><?php echo $row['remaining_amount']; ?></td>
                <td><?php echo $row['payment_type']; ?></td>
                <td><?php echo $row['remaining_payment_date']; ?></td>
                 <td><?php echo $row['total_base_payment']; ?></td>
                <td>
                    <form method="POST" style="display:flex; gap:4px; align-items:center;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="date" name="new_date" required>
                        <button type="submit" name="update_date" class="btn update-btn">Update</button>
                    </form>
                </td>
            </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='8'>No payment reminders for today 🎉</td></tr>";
        }
        ?>
    </table>
</body>
</html>
