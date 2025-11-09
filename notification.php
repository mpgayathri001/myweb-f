<?php
include 'db.php';
date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');
$current_time = date('H:i:s');

/* -------------------------------
   CLEAR LEAD REMINDER
-------------------------------- */
if (isset($_GET['clear_id'])) {
    $id = intval($_GET['clear_id']);
    mysqli_query($conn, "UPDATE leads SET reminder_date='0000-00-00', reminder_time='00:00:00' WHERE id=$id");
    echo "<script>alert('Lead reminder cleared successfully!'); window.location='notification.php';</script>";
    exit;
}

/* -------------------------------
   CLEAR SERVICE REMINDER
-------------------------------- */
if (isset($_GET['clear_service_id'])) {
    $id = intval($_GET['clear_service_id']);
    mysqli_query($conn, "UPDATE customer SET service_cleared=1 WHERE id=$id");
    echo "<script>alert('Service reminder cleared successfully!'); window.location='notification.php';</script>";
    exit;
}

/* -------------------------------
   CLEAR INSURANCE REMINDER
-------------------------------- */
if (isset($_GET['clear_insurance_id'])) {
    $id = intval($_GET['clear_insurance_id']);
    mysqli_query($conn, "UPDATE customer SET insurance_cleared=1 WHERE id=$id");
    echo "<script>alert('Insurance reminder cleared successfully!'); window.location='notification.php';</script>";
    exit;
}

/* -------------------------------
   FETCH LEAD REMINDERS (Waiting List)
-------------------------------- */
$lead_sql = "SELECT * FROM leads 
              WHERE status = 'Waiting List' 
              AND reminder_date != '0000-00-00'
              AND reminder_time != '00:00:00'
              AND reminder_date <= '$today'
              AND reminder_time <= '$current_time'
              ORDER BY reminder_date DESC, reminder_time DESC";
$lead_result = mysqli_query($conn, $lead_sql);

/* -------------------------------
   FETCH CUSTOMER DATA
-------------------------------- */
$customer_sql = "SELECT * FROM customer";
$customer_result = mysqli_query($conn, $customer_sql);

$service_reminders = [];
$insurance_reminders = [];

/* -------------------------------
   CALCULATE SERVICE + INSURANCE REMINDERS
-------------------------------- */
while ($row = mysqli_fetch_assoc($customer_result)) {
    $delivery_date = $row['delivery_date'];
    if ($delivery_date == "0000-00-00" || empty($delivery_date)) continue;

    // Skip cleared
    if ($row['service_cleared'] == 0) {
        $first_service  = date('Y-m-d', strtotime($delivery_date . ' +1 month'));
        $second_service = date('Y-m-d', strtotime($first_service . ' +1 week'));
        $third_service  = date('Y-m-d', strtotime($first_service . ' +2 week'));
        $fourth_service = date('Y-m-d', strtotime($first_service . ' +3 week'));

        if (in_array($today, [$first_service, $second_service, $third_service, $fourth_service])) {
            $service_reminders[] = $row;
        }
    }

    if ($row['insurance_cleared'] == 0) {
        $insurance_due = date('Y-m-d', strtotime($delivery_date . ' +1 year'));
        if ($today == $insurance_due) {
            $insurance_reminders[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications | BMMS Motors</title>
<style>
body { font-family: 'Poppins', sans-serif; background: #111; color: #fff; }
.container { width: 90%; margin: 30px auto; background: #1e1e1e; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #000; }
h2 { color: #ffcc00; text-align: center; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { border: 1px solid #444; padding: 10px; text-align: center; }
th { background: #222; color: #ffcc00; }
button, .clear-btn { background: #ff9800; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; color: #fff; text-decoration: none; }
button:hover, .clear-btn:hover { background: #e68900; }
.clear-btn { background: #c0392b; padding: 5px 10px; }
.clear-btn:hover { background: #e74c3c; }
tr.overdue { background: #661111; }
.section-title { margin-top: 40px; color: #00ffcc; text-align: center; font-size: 1.3em; }
</style>
</head>
<body>
<div class="container">

  <!-- 🔔 LEAD REMINDERS -->
  <h2>📞 Today's & Overdue Lead Reminders</h2>
  <?php if (mysqli_num_rows($lead_result) > 0): ?>
  <table>
    <tr>
      <th>Client Name</th>
      <th>Phone</th>
      <th>Location</th>
      <th>Reminder Date</th>
      <th>Reminder Time</th>
      <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($lead_result)): ?>
      <tr class="<?= ($row['reminder_date'] < $today) ? 'overdue' : ''; ?>">
        <td><?= htmlspecialchars($row['client_name']); ?></td>
        <td><?= htmlspecialchars($row['phone']); ?></td>
        <td><?= htmlspecialchars($row['location']); ?></td>
        <td><?= htmlspecialchars($row['reminder_date']); ?></td>
        <td><?= htmlspecialchars($row['reminder_time']); ?></td>
        <td><a class="clear-btn" href="?clear_id=<?= $row['id']; ?>" onclick="return confirm('Clear this reminder?');">Clear</a></td>
      </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p style="text-align:center; color:#aaa;">✅ No lead reminders pending for today.</p>
  <?php endif; ?>


  <!-- 🛞 SERVICE REMINDERS -->
  <h2 class="section-title">🛞 Free Service Reminders</h2>
  <?php if (count($service_reminders) > 0): ?>
  <table>
    <tr>
      <th>Customer Name</th>
      <th>Phone</th>
      <th>Vehicle No</th>
      <th>Delivery Date</th>
      <th>Service Due Date</th>
      <th>Action</th>
    </tr>
    <?php foreach ($service_reminders as $cust): 
      $delivery_date = $cust['delivery_date'];
      $service_due = date('Y-m-d', strtotime($delivery_date . ' +1 month'));
    ?>
      <tr>
        <td><?= htmlspecialchars($cust['name']); ?></td>
        <td><?= htmlspecialchars($cust['phone']); ?></td>
        <td><?= htmlspecialchars($cust['vehicle_no']); ?></td>
        <td><?= htmlspecialchars($cust['delivery_date']); ?></td>
        <td><?= htmlspecialchars($service_due); ?></td>
        <td><a class="clear-btn" href="?clear_service_id=<?= $cust['id']; ?>" onclick="return confirm('Clear this service reminder?');">Clear</a></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?>
    <p style="text-align:center; color:#aaa;">✅ No service reminders for today.</p>
  <?php endif; ?>


  <!-- 🚘 INSURANCE REMINDERS -->
  <h2 class="section-title">🚘 Insurance Renewal Reminders</h2>
  <?php if (count($insurance_reminders) > 0): ?>
  <table>
    <tr>
      <th>Customer Name</th>
      <th>Phone</th>
      <th>Vehicle No</th>
      <th>Delivery Date</th>
      <th>Insurance Due Date</th>
      <th>Action</th>
    </tr>
    <?php foreach ($insurance_reminders as $cust): 
      $insurance_due = date('Y-m-d', strtotime($cust['delivery_date'] . ' +1 year'));
    ?>
      <tr>
        <td><?= htmlspecialchars($cust['name']); ?></td>
        <td><?= htmlspecialchars($cust['phone']); ?></td>
        <td><?= htmlspecialchars($cust['vehicle_no']); ?></td>
        <td><?= htmlspecialchars($cust['delivery_date']); ?></td>
        <td><?= htmlspecialchars($insurance_due); ?></td>
        <td><a class="clear-btn" href="?clear_insurance_id=<?= $cust['id']; ?>" onclick="return confirm('Clear this insurance reminder?');">Clear</a></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?>
    <p style="text-align:center; color:#aaa;">✅ No insurance reminders for today.</p>
  <?php endif; ?>

</div>
</body>
</html>
