<?php
include 'db.php';

// Handle search query
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM job_card 
          WHERE customer_name LIKE '%$search%' 
          OR vehicle_no LIKE '%$search%' 
          ORDER BY job_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Service History | BMMS Motors</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: url('dashboardimage1.png') no-repeat center center fixed;
  background-size: cover;
  color: #fff;
  min-height: 100vh;
  margin: 0;
  padding: 0;
}

.container {
  width: 90%;
  margin: 40px auto;
  background: rgba(0, 0, 0, 0.6);
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 0 20px rgba(0,0,0,0.4);
}

h2 {
  text-align: center;
  margin-bottom: 30px;
  color: #ffcc00;
  text-shadow: 1px 1px 5px #000;
}

.search-bar {
  text-align: center;
  margin-bottom: 25px;
}

.search-bar input[type="text"] {
  width: 60%;
  padding: 10px 15px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
}

.search-bar button {
  padding: 10px 18px;
  border: none;
  background: #ff9800;
  color: #fff;
  border-radius: 6px;
  font-weight: 500;
  margin-left: 10px;
  cursor: pointer;
  transition: 0.3s;
}

.search-bar button:hover {
  background: #e68900;
  transform: scale(1.05);
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  overflow: hidden;
}

th, td {
  padding: 12px 10px;
  border-bottom: 1px solid rgba(255,255,255,0.2);
  text-align: center;
}

th {
  background: rgba(255,255,255,0.2);
  color: #ffcc00;
  font-size: 15px;
}

td {
  color: #fff;
  font-size: 14px;
}

img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ffcc00;
}

.view-btn {
  background: #ffcc00;
  color: #000;
  padding: 8px 15px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.3s;
}

.view-btn:hover {
  background: #fff;
  color: #000;
  transform: scale(1.05);
}

/* Back button */
.back-btn {
  display: inline-block;
  margin-top: 25px;
  background: #ff9800;
  color: #fff;
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}

.back-btn:hover {
  background: #e68900;
  transform: scale(1.05);
}
</style>
</head>
<body>

<div class="container">
  <h2>📜 View Service History</h2>

  <div class="search-bar">
    <form method="GET" action="">
      <input type="text" name="search" placeholder="Search by Name, Vehicle No" value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit">🔍 Search</button>
    </form>
  </div>

  <table>
    <tr>
      <th>SL No</th>
      <th>Photo</th>
      <th>Customer Name</th>
      <th>Vehicle No</th>
      <th>Phone</th>
      <th>Date</th>
      <th>Time</th>
      <th>View</th>
    </tr>
    <?php
    if ($result && mysqli_num_rows($result) > 0) {
        $slno = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            // Fetch customer photo from customer table
            $vehicle_no = $row['vehicle_no'];
            $photoQuery = "SELECT photo FROM customer WHERE vehicle_no='$vehicle_no' LIMIT 1";
            $photoResult = mysqli_query($conn, $photoQuery);
            $photo = "default_user.png";
            if ($photoResult && mysqli_num_rows($photoResult) > 0) {
                $photoRow = mysqli_fetch_assoc($photoResult);
                $photo = $photoRow['photo'] ?: "default_user.png";
            }
            
            echo "<tr>
                <td>{$slno}</td>
                <td><img src='{$photo}' alt='Customer Photo'></td>
                <td>{$row['customer_name']}</td>
                <td>{$row['vehicle_no']}</td>
                <td>{$row['phone_no']}</td>
                <td>{$row['job_date']}</td>
                <td>{$row['job_time']}</td>
                <td><a href='view_jobcard.php?id={$row['id']}'><button class='view-btn'>View Full Report</button></a></td>
            </tr>";
            $slno++;
        }
    } else {
        echo "<tr><td colspan='8'>No records found</td></tr>";
    }
    ?>
  </table>

  <div style="text-align:center;">
    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
  </div>
</div>

</body>
</html>
