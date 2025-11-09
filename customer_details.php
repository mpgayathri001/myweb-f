<?php
include 'db.php';
$id = $_GET['id'] ?? 0;
$query = "SELECT * FROM customer WHERE id=$id";
$result = mysqli_query($conn, $query);
$customer = mysqli_fetch_assoc($result);

// Decode payment details JSON if available
$payment_details = [];
if (!empty($customer['payment_details'])) {
  $payment_details = json_decode($customer['payment_details'], true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Details | BMMS Motors</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #fff;
  color: #000;
  margin: 40px;
}
.container {
  width: 75%;
  margin: auto;
  border: 2px solid #000;
  padding: 20px;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}
.header img {
  width: 160px;
  height: 160px;
  object-fit: cover;
  border: 2px solid #000;
}
.details {
  width: 100%;
  margin-top: 10px;
  border-top: 2px solid #000;
  padding-top: 10px;
}
.details table {
  width: 100%;
  border-collapse: collapse;
}
.details td {
  border: 1px solid #000;
  padding: 10px;
  font-size: 15px;
  vertical-align: top;
}
.details td b {
  display: inline-block;
  width: 220px;
}
h2 {
  margin-bottom: 10px;
  text-transform: uppercase;
}
.print-btn {
  display: block;
  margin: 20px auto;
  padding: 10px 25px;
  background: #000;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.section-title {
  background: #f1f1f1;
  font-weight: bold;
  padding: 8px;
  border: 1px solid #000;
  text-transform: uppercase;
  text-align: center;
}
.payment-container {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}
.payment-column {
  width: 48%;
  border: 2px dashed #000;
  padding: 10px;
  box-sizing: border-box;
}
.payment-split {
  border: 1px solid #000;
  margin-top: 5px;
  padding: 5px;
}
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <h2>Customer Details</h2>
    <img src="<?php echo $customer['photo'] ?: 'default_user.png'; ?>" alt="Customer Photo">
  </div>

  <div class="details">
    <table>
      <tr><td><b>Name:</b> <?php echo $customer['name']; ?></td></tr>
      <tr><td><b>Phone:</b> <?php echo $customer['phone']; ?></td></tr>
      <tr><td><b>Address:</b> <?php echo $customer['address']; ?></td></tr>
      <tr><td><b>Aadhar:</b> <?php echo $customer['aadhar']; ?></td></tr>
      <tr><td><b>Vehicle Type:</b> <?php echo $customer['vehicle_type']; ?></td></tr>
      <tr><td><b>Ownership Type:</b> <?php echo $customer['ownership_type']; ?></td></tr>
      <tr><td><b>Model:</b> <?php echo $customer['model']; ?></td></tr>
      <tr><td><b>Vehicle No:</b> <?php echo $customer['vehicle_no']; ?></td></tr>
      <tr><td><b>Motor No:</b> <?php echo $customer['motor_no']; ?></td></tr>
      <tr><td><b>Chase No:</b> <?php echo $customer['chassis_no']; ?></td></tr>
      <tr><td><b>Controller No:</b> <?php echo $customer['controller_no']; ?></td></tr>
      <tr><td><b>Source:</b> <?php echo $customer['source']; ?></td></tr>
      <tr><td><b>Registration Date:</b> <?php echo $customer['registration_date']; ?></td></tr>
      <tr><td><b>Delivery Date:</b> <?php echo $customer['delivery_date']; ?></td></tr>

      <tr><td class="section-title">Payment Information</td></tr>
      <tr>
        <td>
          <div class="payment-container">
            <!-- Left Column: UPI & Cash -->
            <div class="payment-column">
              <b> 💵Ready Cash Payments</b><br>
              <?php
              $left_modes = ['upi', 'cash'];
              $found_left = false;
              foreach ($left_modes as $mode) {
                if (!empty($payment_details[$mode])) {
                  $found_left = true;
                  echo "<b><u>" . strtoupper($mode) . "</u></b><br>";
                  foreach ($payment_details[$mode] as $entry) {
                    echo "<div class='payment-split'>
                            <b>Amount:</b> ₹{$entry['amount']}<br>
                            <b>Date:</b> {$entry['date']}<br>
                            <b>Mode:</b> " . ucfirst($entry['mode']) . "
                          </div>";
                  }
                }
              }
              if (!$found_left) echo "<i>No UPI or Cash payment details</i>";
              ?>
            </div>

            <!-- Right Column: Cheque & Finance -->
            <div class="payment-column">
              <b> 🏦 Finance Payments</b><br>
              <?php
              $right_modes = ['cheque', 'finance'];
              $found_right = false;
              foreach ($right_modes as $mode) {
                if (!empty($payment_details[$mode])) {
                  $found_right = true;
                  echo "<b><u>" . strtoupper($mode) . "</u></b><br>";
                  if ($mode == 'finance') {
                    echo "<b>Bank Name:</b> {$customer['bank_name']}<br>
                          <b>Branch:</b> {$customer['branch_name']}<br>
                          <b>Address:</b> {$customer['bank_address']}<br><br>";
                  }
                  foreach ($payment_details[$mode] as $entry) {
                    echo "<div class='payment-split'>
                            <b>Amount:</b> ₹{$entry['amount']}<br>
                            <b>Date:</b> {$entry['date']}<br>
                            <b>Mode:</b> " . ucfirst($entry['mode']) . "
                          </div>";
                  }
                }
              }
              if (!$found_right) echo "<i>No Cheque or Finance payment details</i>";
              ?>
            </div>
          </div>
        </td>
      </tr>

      <tr><td class="section-title">Discount Details</td></tr>
      <tr>
        <td>
          <b>Discount Amount:</b> ₹<?php echo $customer['discount_percent']; ?><br>
          <b>Discount Given By:</b> <?php echo $customer['discount_given_by']; ?>
        </td>
      </tr>
    </table>

    <p>***Customer Details are Authorized. Do not share with unauthorized persons.***</p>
  </div>
</div>

<button class="print-btn" onclick="window.print()">🖨 Print</button>

</body>
</html>
