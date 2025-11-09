<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

// Default query
$where = "";
$params = [];
$filter_label = "All Expenses";

// Filter by date or month
if (isset($_GET['from_date']) && isset($_GET['to_date']) && $_GET['from_date'] && $_GET['to_date']) {
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
    $where = "WHERE date BETWEEN ? AND ?";
    $params = [$from_date, $to_date];
    $filter_label = "Expenses from $from_date to $to_date";
} elseif (isset($_GET['month']) && $_GET['month']) {
    $month = $_GET['month'];
    $where = "WHERE DATE_FORMAT(date, '%Y-%m') = ?";
    $params = [$month];
    $filter_label = "Expenses for " . date("F Y", strtotime($month . "-01"));
}

$query = "SELECT * FROM daily_expense $where ORDER BY date DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Daily Expense | BMMS Motors</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('admindashboardimage.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      min-height: 100vh;
      padding: 40px;
    }

    h1 {
      text-align: center;
      color: #ffeb3b;
      margin-bottom: 20px;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
    }

    form {
      text-align: center;
      margin-bottom: 25px;
      background: rgba(0,0,0,0.5);
      display: inline-block;
      padding: 15px 25px;
      border-radius: 12px;
    }

    input[type="date"], input[type="month"] {
      padding: 8px;
      border-radius: 6px;
      border: none;
      outline: none;
      margin: 0 5px;
    }

    button {
      background: #ff9800;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #ffa726;
      transform: scale(1.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(0,0,0,0.6);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    th {
      background: rgba(255,255,255,0.2);
      color: #fff;
    }

    tr:hover {
      background: rgba(255,255,255,0.1);
    }

    img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid white;
      transition: 0.3s;
      cursor: pointer;
    }

    img:hover {
      transform: scale(1.3);
    }

    .total-box {
      text-align: right;
      font-size: 20px;
      font-weight: bold;
      color: #ffeb3b;
      margin-top: 20px;
      background: rgba(0,0,0,0.6);
      padding: 10px 15px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.4);
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 20px;
      background: #ff9800;
      padding: 10px 20px;
      border-radius: 8px;
      color: white;
      text-decoration: none;
      transition: 0.3s;
    }

    .back-btn:hover {
      background: #ffa726;
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
      table, th, td {
        font-size: 14px;
      }
      img {
        width: 50px;
        height: 50px;
      }
    }
  </style>
</head>
<body>
  <a href="dailyexpense.php" class="back-btn">← Add New Expense</a>
  <h1>View Daily Expenses</h1>

  <form method="GET">
    <label>From:</label>
    <input type="date" name="from_date">
    <label>To:</label>
    <input type="date" name="to_date">
    <button type="submit">Filter</button>
    <span style="margin: 0 10px; color: #ccc;">OR</span>
    <label>Month:</label>
    <input type="month" name="month">
    <button type="submit">View Month</button>
  </form>

  <h2 style="text-align:center; color:#fff; margin-bottom:15px;"><?php echo htmlspecialchars($filter_label); ?></h2>

  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Item Description</th>
        <th>Amount (₹)</th>
        <th>Total (₹)</th>
        <th>Images</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $grand_total = 0;
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $grand_total += $row['amount'];
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['date']) . "</td>";
              echo "<td>" . htmlspecialchars($row['item_description']) . "</td>";
              echo "<td>₹" . number_format($row['amount'], 2) . "</td>";
              echo "<td>₹" . number_format($row['total'], 2) . "</td>";
              echo "<td>";
              if (!empty($row['image_path'])) {
                  $images = explode(",", $row['image_path']);
                  foreach ($images as $img) {
                      echo "<a href='" . htmlspecialchars($img) . "' target='_blank'><img src='" . htmlspecialchars($img) . "' alt='Expense Image'></a> ";
                  }
              } else {
                  echo "No Image";
              }
              echo "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='5'>No expenses found for the selected period.</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <div class="total-box">
    <?php
      if (isset($month) && $month) {
          echo "Total for " . date("F Y", strtotime($month . "-01")) . ": ₹ " . number_format($grand_total, 2);
      } else {
          echo "Grand Total: ₹ " . number_format($grand_total, 2);
      }
    ?>
  </div>
</body>
</html>
