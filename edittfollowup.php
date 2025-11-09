<?php
include("db.php");
date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Ensure ID is present
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid access! ID missing.'); window.location.href='view_followup.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Fetch existing record
$stmt = $conn->prepare("SELECT * FROM followup WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$followup = $result->fetch_assoc();
$stmt->close();

if (!$followup) {
    echo "<script>alert('Record not found!'); window.location.href='view_followup.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Read form
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    // use the column name that exists in your table (customer_aadhar)
    $customer_aadhar = $_POST['customer_aadhar'];
    $followup_date = $_POST['followup_date'];
    $vehicle_type = $_POST['vehicle_type'];
    $vehicle_model = $_POST['vehicle_model'];
    $vehicle_range = $_POST['vehicle_range'];
    $onroad_amount = floatval($_POST['onroad_amount']);

    // Base payments
    $base_payment_details = [];
    $total_base_payment = 0.0;
    if (isset($_POST['base_payment_amount']) && is_array($_POST['base_payment_amount'])) {
        for ($i = 0; $i < count($_POST['base_payment_amount']); $i++) {
            $amount = floatval($_POST['base_payment_amount'][$i]);
            $date = $_POST['base_payment_date'][$i] ?? '';
            $mode = $_POST['base_payment_mode'][$i] ?? '';
            if ($amount > 0) {
                $base_payment_details[] = [
                    "amount" => $amount,
                    "date" => $date,
                    "mode" => $mode
                ];
                $total_base_payment += $amount;
            }
        }
    }

    $base_payment_json = json_encode($base_payment_details, JSON_UNESCAPED_SLASHES);
    $remaining_amount = $onroad_amount - $total_base_payment;
    $remaining_payment_date = $_POST['remaining_payment_date'];
    $payment_type = $_POST['payment_type'];
    $bank_name = ($payment_type === "Finance") ? ($_POST['bank_name'] ?? '') : '';
    $bank_location = ($payment_type === "Finance") ? ($_POST['bank_location'] ?? '') : '';

    // Prepare UPDATE statement
    $update_sql = "UPDATE followup SET 
        customer_name=?, customer_phone=?, customer_aadhar=?, followup_date=?, vehicle_type=?, vehicle_model=?, vehicle_range=?, 
        onroad_amount=?, base_payment_details=?, total_base_payment=?, remaining_amount=?, remaining_payment_date=?, 
        payment_type=?, bank_name=?, bank_location=? 
        WHERE id=?";

    $update = $conn->prepare($update_sql);
    if (!$update) {
        die("Prepare failed (UPDATE): " . $conn->error);
    }

    // Types and parameters:
    // 1 customer_name (s)
    // 2 customer_phone (s)
    // 3 customer_aadhar (s)
    // 4 followup_date (s)
    // 5 vehicle_type (s)
    // 6 vehicle_model (s)
    // 7 vehicle_range (s)
    // 8 onroad_amount (d)
    // 9 base_payment_json (s)
    //10 total_base_payment (d)
    //11 remaining_amount (d)
    //12 remaining_payment_date (s)
    //13 payment_type (s)
    //14 bank_name (s)
    //15 bank_location (s)
    //16 id (i)
    $types = "sssssssdsddssssi"; // exactly 16 type specifiers

    $update->bind_param(
        $types,
        $customer_name,
        $customer_phone,
        $customer_aadhar,
        $followup_date,
        $vehicle_type,
        $vehicle_model,
        $vehicle_range,
        $onroad_amount,
        $base_payment_json,
        $total_base_payment,
        $remaining_amount,
        $remaining_payment_date,
        $payment_type,
        $bank_name,
        $bank_location,
        $id
    );

    if ($update->execute()) {
        // Send Gmail notification (PHPMailer)
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mpgayathri001@gmail.com'; // replace
            $mail->Password = 'hhsq mldc aree enkx';     // replace with app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('mpgayathri001@gmail.com', 'BMMS Motors');
            $mail->addAddress('mpbalanmurugan@gmail.com', 'CEO');

            $mail->isHTML(true);
            $mail->Subject = "Follow-up Updated - " . htmlspecialchars($customer_name);
            // build payment rows for email
            $payment_rows = "";
            foreach ($base_payment_details as $bp) {
                $payment_rows .= "<tr><td>₹" . number_format($bp['amount'],2) . "</td><td>" . htmlspecialchars($bp['date']) . "</td><td>" . htmlspecialchars($bp['mode']) . "</td></tr>";
            }
            $mail->Body = "
                <h2>Updated Follow-up Details</h2>
                <table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
                    <tr><th>Customer Name</th><td>" . htmlspecialchars($customer_name) . "</td></tr>
                    <tr><th>Phone</th><td>" . htmlspecialchars($customer_phone) . "</td></tr>
                    <tr><th>Aadhar</th><td>" . htmlspecialchars($customer_aadhar) . "</td></tr>
                    <tr><th>Vehicle</th><td>" . htmlspecialchars($vehicle_type) . " (" . htmlspecialchars($vehicle_model) . ")</td></tr>
                    <tr><th>Follow-up Date</th><td>" . htmlspecialchars($followup_date) . "</td></tr>
                    <tr><th>Onroad Amount</th><td>₹" . number_format($onroad_amount,2) . "</td></tr>
                    <tr><th>Total Base Payment</th><td>₹" . number_format($total_base_payment,2) . "</td></tr>
                    <tr><th>Remaining Amount</th><td>₹" . number_format($remaining_amount,2) . "</td></tr>
                    <tr><th>Payment Type</th><td>" . htmlspecialchars($payment_type) . "</td></tr>
                    <tr><th>Remaining Payment Date</th><td>" . htmlspecialchars($remaining_payment_date) . "</td></tr>
                </table>
                <br>
                <h3>Base Payments</h3>
                <table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
                    <tr><th>Amount</th><th>Date</th><th>Mode</th></tr>
                    $payment_rows
                </table>
            ";
            $mail->send();
        } catch (Exception $e) {
            // Log but don't break flow
            error_log("Mail error: " . $mail->ErrorInfo);
        }

        echo "<script>alert('Follow-up updated'); window.location.href='viewfollowup.php';</script>";
        exit;
    } else {
        echo "<script>alert('Update failed: " . addslashes($update->error) . "');</script>";
    }

    $update->close();
}

// decode existing payments for form
$base_payments = json_decode($followup['base_payment_details'], true) ?? [];

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Follow-up</title>
  <style>
    body { font-family: Arial; background:#f2f5f9; padding:40px; }
    form { background:#fff; padding:25px; border-radius:10px; width:720px; margin:auto; box-shadow:0 0 10px rgba(0,0,0,0.1); }
    input, select { width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc; border-radius:4px; }
    .payment-group { display:flex; gap:10px; margin-bottom:10px; }
    .payment-group input, .payment-group select { flex:1; }
    button { background:green; color:white; padding:10px; border:none; border-radius:5px; cursor:pointer; }
    button:hover { background:darkgreen; }
    h2 { text-align:center; color:#333; }
    .readonly { background:#eee; font-weight:bold; }
  </style>
</head>
<body>

<h2>Edit Follow-up</h2>
<form method="POST">
  <label>Customer Name:</label>
  <input type="text" name="customer_name" value="<?= htmlspecialchars($followup['customer_name']) ?>" required>

  <label>Phone:</label>
  <input type="text" name="customer_phone" value="<?= htmlspecialchars($followup['customer_phone']) ?>" required>

  <label>Aadhar Number:</label>
  <input type="text" name="customer_aadhar" value="<?= htmlspecialchars($followup['customer_aadhar'] ?? '') ?>" required>

  <label>Follow-up Date:</label>
  <input type="date" name="followup_date" value="<?= htmlspecialchars($followup['followup_date']) ?>" required>

  <label>Vehicle Type:</label>
  <select name="vehicle_type" required>
    <option value="T-Board" <?= $followup['vehicle_type']=="T-Board"?"selected":"" ?>>T-Board</option>
    <option value="Own Board" <?= $followup['vehicle_type']=="Own Board"?"selected":"" ?>>Own Board</option>
  </select>

  <label>Vehicle Model:</label>
  <select name="vehicle_model" required>
    <option value="Loader PV" <?= $followup['vehicle_model']=="Loader PV"?"selected":"" ?>>Loader PV</option>
    <option value="Loader DV" <?= $followup['vehicle_model']=="Loader DV"?"selected":"" ?>>Loader DV</option>
    <option value="Metro XL" <?= $followup['vehicle_model']=="Metro XL"?"selected":"" ?>>Metro XL</option>
  </select>

  <label>Vehicle Range (km):</label>
  <input type="text" name="vehicle_range" value="<?= htmlspecialchars($followup['vehicle_range']) ?>" required>

  <label>Vehicle On-road Amount (₹):</label>
  <input type="number" name="onroad_amount" id="onroad_amount" value="<?= htmlspecialchars($followup['onroad_amount']) ?>" oninput="calculateRemaining()" required>

  <div class="base-payments">
    <label>Base Payments:</label>
    <div id="basePaymentsContainer">
      <?php if (!empty($base_payments)): ?>
        <?php foreach ($base_payments as $p): ?>
          <div class="payment-group">
            <input type="number" name="base_payment_amount[]" value="<?= htmlspecialchars($p['amount']) ?>" oninput="calculateRemaining()">
            <input type="date" name="base_payment_date[]" value="<?= htmlspecialchars($p['date']) ?>">
            <select name="base_payment_mode[]">
              <option value="UPI" <?= $p['mode']=="UPI"?"selected":"" ?>>UPI</option>
              <option value="Cash" <?= $p['mode']=="Cash"?"selected":"" ?>>Cash</option>
              <option value="Cheque" <?= $p['mode']=="Cheque"?"selected":"" ?>>Cheque</option>
            </select>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="payment-group">
          <input type="number" name="base_payment_amount[]" placeholder="Amount (₹)" oninput="calculateRemaining()">
          <input type="date" name="base_payment_date[]">
          <select name="base_payment_mode[]">
            <option value="UPI">UPI</option>
            <option value="Cash">Cash</option>
            <option value="Cheque">Cheque</option>
          </select>
        </div>
      <?php endif; ?>
    </div>
    <button type="button" onclick="addPayment()">+ Add More</button>
  </div>

  <label>Total Base Payment (₹):</label>
  <input type="text" id="total_base_payment" class="readonly" readonly>

  <label>Remaining Amount (₹):</label>
  <input type="text" id="remaining_amount" class="readonly" readonly>

  <label>Remaining Payment Date:</label>
  <input type="date" name="remaining_payment_date" value="<?= htmlspecialchars($followup['remaining_payment_date']) ?>" required>

  <label>Payment Type:</label>
  <input type="radio" name="payment_type" value="Finance" <?= $followup['payment_type']=="Finance"?"checked":"" ?> onclick="toggleBank(true)"> Finance
  <input type="radio" name="payment_type" value="Ready Cash" <?= $followup['payment_type']=="Ready Cash"?"checked":"" ?> onclick="toggleBank(false)"> Ready Cash

  <div id="bankDetails" style="display:<?= ($followup['payment_type']=="Finance") ? 'block' : 'none' ?>;">
    <label>Bank Name:</label>
    <input type="text" name="bank_name" value="<?= htmlspecialchars($followup['bank_name']) ?>">
    <label>Bank Location:</label>
    <input type="text" name="bank_location" value="<?= htmlspecialchars($followup['bank_location']) ?>">
  </div>

  <br><br>
  <button type="submit">Update</button>
</form>

<script>
function addPayment() {
  const div = document.createElement('div');
  div.className = 'payment-group';
  div.innerHTML = `
    <input type="number" name="base_payment_amount[]" placeholder="Amount (₹)" oninput="calculateRemaining()">
    <input type="date" name="base_payment_date[]">
    <select name="base_payment_mode[]">
      <option value="UPI">UPI</option>
      <option value="Cash">Cash</option>
      <option value="Cheque">Cheque</option>
    </select>`;
  document.getElementById('basePaymentsContainer').appendChild(div);
}

function toggleBank(show) {
  document.getElementById('bankDetails').style.display = show ? 'block' : 'none';
}

function calculateRemaining() {
  const onroad = parseFloat(document.getElementById('onroad_amount').value) || 0;
  const baseAmounts = document.querySelectorAll('input[name="base_payment_amount[]"]');
  let totalBase = 0;
  baseAmounts.forEach(input => totalBase += parseFloat(input.value) || 0);
  document.getElementById('total_base_payment').value = totalBase.toFixed(2);
  document.getElementById('remaining_amount').value = (onroad - totalBase).toFixed(2);
}

// Initialize totals
window.onload = calculateRemaining;
</script>

</body>
</html>
