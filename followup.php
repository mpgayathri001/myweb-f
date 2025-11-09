<?php
include("db.php");
date_default_timezone_set('Asia/Kolkata');

// ✅ Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $customer_aadhar = $_POST['customer_aadhar'];
    $followup_date = $_POST['followup_date'];
    $vehicle_type = $_POST['vehicle_type'];
    $vehicle_model = $_POST['vehicle_model'];
    $vehicle_range = $_POST['vehicle_range'];
    $onroad_amount = floatval($_POST['onroad_amount']);

    // ✅ Collect base payment details
    $base_payment_details = [];
    $total_base_payment = 0;

    if (isset($_POST['base_payment_amount']) && is_array($_POST['base_payment_amount'])) {
        for ($i = 0; $i < count($_POST['base_payment_amount']); $i++) {
            $amount = floatval($_POST['base_payment_amount'][$i]);
            $date = $_POST['base_payment_date'][$i];
            $mode = $_POST['base_payment_mode'][$i];
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
    $bank_name = $payment_type === "Finance" ? $_POST['bank_name'] : '';
    $bank_location = $payment_type === "Finance" ? $_POST['bank_location'] : '';

    // ✅ Insert into DB
    $stmt = $conn->prepare("INSERT INTO followup 
        (customer_name, customer_phone, customer_aadhar, followup_date, vehicle_type, vehicle_model, vehicle_range, onroad_amount, base_payment_details, total_base_payment, remaining_amount, remaining_payment_date, payment_type, bank_name, bank_location)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssssssdsdsssss",
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
        $bank_location
    );

    if ($stmt->execute()) {
        // ✅ Send Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mpgayathri001@gmail.com'; // your Gmail
            $mail->Password = 'hhsq mldc aree enkx';     // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('mpgayathri001@gmail.com', 'BMMS Motors');
            $mail->addAddress('mpbalanmurugan@gmail.com', 'CEO'); // CEO mail

            $mail->isHTML(true);
            $mail->Subject = "New Follow-up Entry - $customer_name";
            $mail->Body = "
                <h2>New Follow-up Details</h2>
                <p><b>Customer Name:</b> $customer_name</p>
                <p><b>Phone:</b> $customer_phone</p>
                <p><b>Aadhar:</b> $customer_aadhar</p>
                <p><b>Follow-up Date:</b> $followup_date</p>
                <p><b>Vehicle Type:</b> $vehicle_type</p>
                <p><b>Vehicle Model:</b> $vehicle_model</p>
                <p><b>Vehicle Range:</b> $vehicle_range km</p>
                <p><b>On-road Amount:</b> ₹$onroad_amount</p>
                <p><b>Total Base Payment:</b> ₹$total_base_payment</p>
                <p><b>Remaining Amount:</b> ₹$remaining_amount</p>
                <p><b>Payment Type:</b> $payment_type</p>
                <p><b>Remaining Payment Date:</b> $remaining_payment_date</p>
                <p><b>Bank:</b> $bank_name ($bank_location)</p>
            ";

            $mail->send();
            echo "<script>alert('Follow-up saved successfully!'); window.location.href='followup.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Saved, but email failed: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Database Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Follow-up Entry</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; margin: 40px; }
        form { background: #fff; padding: 25px; border-radius: 10px; width: 650px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .base-payments { background: #fafafa; padding: 10px; border-radius: 8px; margin-bottom: 10px; }
        .payment-group { display: flex; gap: 10px; margin-bottom: 10px; }
        .payment-group input, .payment-group select { flex: 1; }
        button { background: green; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background: darkgreen; }
        h2 { text-align: center; color: #333; }
        .readonly { background: #eee; font-weight: bold; }
    </style>
</head>
<body>

<h2>Follow-Up Entry</h2>

<form method="POST">
    <label>Customer Name:</label>
    <input type="text" name="customer_name" required>

    <label>Customer Phone:</label>
    <input type="text" name="customer_phone" required maxlength="10">

    <label>Aadhar Number:</label>
    <input type="text" name="customer_aadhar" required maxlength="12">

    <label>Follow-up Date:</label>
    <input type="date" name="followup_date" required>

    <label>Vehicle Type:</label>
    <select name="vehicle_type" required>
        <option value="T-Board">T-Board</option>
        <option value="Own Board">Own Board</option>
    </select>

    <label>Vehicle Model:</label>
    <select name="vehicle_model" required>
        <option value="Loader PV">Loader PV</option>
        <option value="Loader DV">Loader DV</option>
        <option value="Metro XL">Metro XL</option>
    </select>

    <label>Vehicle Range (km):</label>
    <input type="text" name="vehicle_range" required>

    <label>Vehicle On-road Amount (₹):</label>
    <input type="number" name="onroad_amount" id="onroad_amount" required oninput="calculateRemaining()">

    <div class="base-payments">
        <label>Base Payments:</label>
        <div id="basePaymentsContainer">
            <div class="payment-group">
                <input type="number" name="base_payment_amount[]" placeholder="Amount (₹)" oninput="calculateRemaining()">
                <input type="date" name="base_payment_date[]">
                <select name="base_payment_mode[]">
                    <option value="UPI">UPI</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque">Cheque</option>
                </select>
            </div>
        </div>
        <button type="button" onclick="addPayment()">+ Add More</button>
    </div>

    <label>Total Base Payment (₹):</label>
    <input type="text" id="total_base_payment" class="readonly" readonly>

    <label>Remaining Amount (₹):</label>
    <input type="text" id="remaining_amount" class="readonly" readonly>

    <label>Remaining Payment Date:</label>
    <input type="date" name="remaining_payment_date" required>

    <label>Payment Type:</label>
    <input type="radio" name="payment_type" value="Finance" onclick="toggleBank(true)"> Finance
    <input type="radio" name="payment_type" value="Ready Cash" onclick="toggleBank(false)" checked> Ready Cash

    <div id="bankDetails" style="display:none;">
        <label>Bank Name:</label>
        <input type="text" name="bank_name">
        <label>Bank Location:</label>
        <input type="text" name="bank_location">
    </div>

    <button type="submit">Submit</button>
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
        </select>
    `;
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
    const remaining = onroad - totalBase;
    document.getElementById('total_base_payment').value = totalBase.toFixed(2);
    document.getElementById('remaining_amount').value = remaining.toFixed(2);
}
</script>

</body>
</html>
