<?php
session_start();
include("db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Marketing - BMMS MOTORS</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('9583201.jpg') no-repeat center center/cover;
      color: white;
      margin: 0;
      padding: 0;
    }
    header {
      background: transparent;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
    }
    header img {
      height: 60px;
    }
    h1 {
      color: #fff;
      text-align: center;
      margin-top: 10px;
    }
    form.filter {
      background: rgba(0,0,0,0.5);
      padding: 15px;
      border-radius: 10px;
      width: 90%;
      margin: 20px auto;
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
    }
    form.filter input, form.filter select, form.filter button {
      padding: 8px 12px;
      border-radius: 8px;
      border: none;
      outline: none;
      font-size: 14px;
    }
    form.filter button {
      background-color: #ffcc00;
      cursor: pointer;
      font-weight: bold;
    }
    table {
      width: 95%;
      margin: 20px auto;
      border-collapse: collapse;
      background: rgba(0,0,0,0.6);
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #444;
      text-align: center;
      color: #fff;
    }
    th {
      background: rgba(255, 204, 0, 0.3);
    }
    img.proof {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 8px;
    }
    footer {
      text-align: center;
      color: #ccc;
      margin: 30px 0;
      font-size: 14px;
    }
  </style>
</head>
<body>

<header>
  <div>
    <img src="bmmslogo.png" alt="BMMS Logo">
  </div>
  <h2>BMMS MOTORS - Marketing Records</h2>
</header>

<form class="filter" method="GET" action="">
  <div>
    <label>Date From:</label>
    <input type="date" name="from_date" value="<?php echo $_GET['from_date'] ?? ''; ?>">
  </div>
  <div>
    <label>Date To:</label>
    <input type="date" name="to_date" value="<?php echo $_GET['to_date'] ?? ''; ?>">
  </div>
  <div>
    <label>Location:</label>
    <input type="text" name="location" placeholder="Enter location" value="<?php echo $_GET['location'] ?? ''; ?>">
  </div>
  <button type="submit">Filter</button>
  <a href="view_marketing.php" style="background:#fff;color:#000;padding:8px 12px;border-radius:8px;text-decoration:none;">Clear</a>
</form>

<?php
// Prepare SQL filters
$where = [];
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = $_GET['from_date'];
    $to = $_GET['to_date'];
    $where[] = "date_of_visit BETWEEN '$from' AND '$to'";
}
if (!empty($_GET['location'])) {
    $loc = mysqli_real_escape_string($conn, $_GET['location']);
    $where[] = "locations LIKE '%$loc%'";
}

$query = "SELECT * FROM marketing_details";
if (count($where) > 0) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY date_of_visit DESC";

$result = mysqli_query($conn, $query);
?>

<h1>Marketing Details</h1>

<table>
  <tr>
    <th>ID</th>
    <th>Date of Visit</th>
    <th>Locations</th>
    <th>Employees</th>
    <th>Total Amount</th>
    <th>Expense Details</th>
    <th>Proof Images</th>
    <th>Added By</th>
  </tr>

<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['date_of_visit']}</td>";
        echo "<td>{$row['locations']}</td>";
        echo "<td>{$row['employees']}</td>";
        echo "<td>₹{$row['total_amount']}</td>";
        
        // Decode expenses
        $expenses = json_decode($row['expense_details'], true);
        echo "<td>";
        if ($expenses) {
            foreach ($expenses as $exp) {
                echo $exp['desc'] . " - ₹" . $exp['amt'] . "<br>";
            }
        } else {
            echo "N/A";
        }
        echo "</td>";

        // Proof images
        echo "<td>";
        if (!empty($row['proof_images'])) {
            $images = explode(",", $row['proof_images']);
            foreach ($images as $img) {
                $img = trim($img);
                echo "<a href='uploads/marketing/$img' target='_blank'><img src='uploads/marketing/$img' class='proof'></a> ";
            }
        } else {
            echo "No Images";
        }
        echo "</td>";

        echo "<td>{$row['added_by']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No records found.</td></tr>";
}
?>
</table>

<footer>
  <p>© 2025 BMMS MOTORS | Marketing Management System</p>
</footer>

</body>
</html>
