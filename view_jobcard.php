<?php
include 'db.php';
$id = $_GET['id'] ?? 0;

// Fetch job card details
$query = "SELECT * FROM job_card WHERE id = $id";
$result = mysqli_query($conn, $query);
$job = mysqli_fetch_assoc($result);

if (!$job) {
    die("<h3 style='text-align:center;'>No job card found.</h3>");
}

// Fetch customer photo if available
$vehicle_no = $job['vehicle_no'];
$photoQuery = "SELECT photo FROM customer WHERE vehicle_no = '$vehicle_no' LIMIT 1";
$photoResult = mysqli_query($conn, $photoQuery);
$photo = "default_user.png";
if ($photoResult && mysqli_num_rows($photoResult) > 0) {
    $photoRow = mysqli_fetch_assoc($photoResult);
    $photo = $photoRow['photo'] ?: "default_user.png";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Service Report | BMMS Motors</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #fff;
  color: #000;
  margin: 0;
  padding: 30px 0;
}
.container {
  width: 70%;
  margin: auto;
  border: 2px solid #000;
  border-radius: 10px;
  padding: 25px 40px;
  box-shadow: 0 0 8px rgba(0,0,0,0.2);
  background: #fff;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #000;
  padding-bottom: 10px;
  margin-bottom: 20px;
}
.header img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid #000;
}
.header h2 {
  text-align: center;
  flex: 1;
  color: #000;
  font-size: 20px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  font-size: 14px;
}
td, th {
  border: 1px solid #000;
  padding: 6px 10px;
  text-align: left;
  vertical-align: top;
}
th {
  background: #f2f2f2;
}
.section-title {
  background: #f5f5f5;
  font-weight: bold;
  text-align: center;
  border: 1px solid #000;
  padding: 6px;
  margin-top: 20px;
  font-size: 15px;
}
.print-btn {
  display: block;
  margin: 25px auto 10px;
  padding: 10px 20px;
  background: #000;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 15px;
}
.print-btn:hover {
  background: #444;
}
@media print {
  .print-btn {
    display: none;
  }
  .container {
    border: none;
    box-shadow: none;
    width: 90%;
    padding: 0;
  }
}
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <img src="<?php echo htmlspecialchars($photo); ?>" alt="Customer Photo">
    <h2>BMMS MOTORS<br>Service Report</h2>
  </div>

  <div class="section-title">Job Details</div>
  <table>
    <tr><th>Job Card No</th><td><?php echo $job['job_card_no']; ?></td></tr>
    <tr><th>Job Date</th><td><?php echo $job['job_date']; ?></td></tr>
    <tr><th>Job Time</th><td><?php echo $job['job_time']; ?></td></tr>
  </table>

  <div class="section-title">Customer Information</div>
  <table>
    <tr><th>Name</th><td><?php echo $job['customer_name']; ?></td></tr>
    <tr><th>Address</th><td><?php echo $job['customer_address']; ?></td></tr>
    <tr><th>Phone No</th><td><?php echo $job['phone_no']; ?></td></tr>
    <tr><th>Email</th><td><?php echo $job['email']; ?></td></tr>
  </table>

  <div class="section-title">Vehicle Information</div>
  <table>
    <tr><th>Vehicle No</th><td><?php echo $job['vehicle_no']; ?></td></tr>
    <tr><th>Model</th><td><?php echo $job['model']; ?></td></tr>
    <tr><th>Chassis No</th><td><?php echo $job['chassis_no']; ?></td></tr>
    <tr><th>Motor No</th><td><?php echo $job['motor_no']; ?></td></tr>
    <tr><th>KM Reading</th><td><?php echo $job['km_reading']; ?></td></tr>
  </table>

  <div class="section-title">Service Details</div>
  <table>
    <tr><th>Type of Service</th><td><?php echo $job['type_of_service']; ?></td></tr>
    <tr><th>Tyre Pressure (Front)</th><td><?php echo $job['tyre_pressure_front']; ?></td></tr>
    <tr><th>Tyre Pressure (Rear)</th><td><?php echo $job['tyre_pressure_rear']; ?></td></tr>
    <tr><th>Last Service Date</th><td><?php echo $job['last_service_date']; ?></td></tr>
    <tr><th>Average KM</th><td><?php echo $job['average_km']; ?></td></tr>
    <tr><th>Last Service Work</th><td><?php echo $job['last_service_work']; ?></td></tr>
    <tr><th>Customer Complaint</th><td><?php echo $job['customer_complaint']; ?></td></tr>
    <tr><th>Jobs to Perform</th><td><?php echo $job['jobs_to_perform']; ?></td></tr>
    <tr><th>Approval Taken</th><td><?php echo $job['approval_taken']; ?></td></tr>
    <tr><th>Observation</th><td><?php echo $job['observation']; ?></td></tr>
    <tr><th>Action Taken</th><td><?php echo $job['action_taken']; ?></td></tr>
  </table>

  <div class="section-title">Estimate & Payment</div>
  <table>
    <tr><th>Estimate (Parts)</th><td><?php echo $job['estimate_parts']; ?></td></tr>
    <tr><th>Estimate (Labour)</th><td><?php echo $job['estimate_labour']; ?></td></tr>
    <tr><th>Estimate (Consumables)</th><td><?php echo $job['estimate_consumables']; ?></td></tr>
    <tr><th>Payment Mode</th><td><?php echo $job['payment_mode']; ?></td></tr>
  </table>

  <div class="section-title">Delivery Details</div>
  <table>
    <tr><th>Delivery Date & Time</th><td><?php echo $job['delivery_datetime']; ?></td></tr>
    <tr><th>Completion Status</th><td><?php echo $job['completion_status']; ?></td></tr>
    <tr><th>Service Advisor</th><td><?php echo $job['service_advisor']; ?></td></tr>
  </table>
</div>

<button class="print-btn" onclick="window.print()">🖨 Print</button>

</body>
</html>
