<?php
include("db.php");
date_default_timezone_set('Asia/Kolkata');

// ✅ Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $chasis_no = $_POST['chasis_no'];
    $rto_amount = floatval($_POST['rto_amount']);
    $rto_date = $_POST['rto_date'];
    $insurance_amount = floatval($_POST['insurance_amount']);
    $insurance_date = $_POST['insurance_date'];
    $vehicle_incoming_date = $_POST['vehicle_incoming_date'];

    // ✅ Collect additional expenses
    $extra_expenses = [];
    $total_extra = 0;
    $extra_table_rows = "";

    if (isset($_POST['desc']) && is_array($_POST['desc'])) {
        for ($i = 0; $i < count($_POST['desc']); $i++) {
            $desc = trim($_POST['desc'][$i]);
            $amount = floatval($_POST['amount'][$i]);
            if ($desc !== "" && $amount > 0) {
                $extra_expenses[] = ["desc" => $desc, "amount" => $amount];
                $total_extra += $amount;
                $extra_table_rows .= "<tr><td>$desc</td><td>₹" . number_format($amount, 2) . "</td></tr>";
            }
        }
    }

    $extra_expenses_json = json_encode($extra_expenses, JSON_UNESCAPED_SLASHES);
    $final_total = $rto_amount + $insurance_amount + $total_extra;

    // ✅ Save to Database
    $stmt = $conn->prepare("INSERT INTO vehicle_expense 
        (chasis_no, rto_amount, rto_date, insurance_amount, insurance_date, vehicle_incoming_date, extra_expenses, total_expense)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sdsdsssd",
        $chasis_no,
        $rto_amount,
        $rto_date,
        $insurance_amount,
        $insurance_date,
        $vehicle_incoming_date,
        $extra_expenses_json,
        $final_total
    );

    if ($stmt->execute()) {
        // ✅ Send Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mpgayathri001@gmail.com'; // 🔁 your Gmail
            $mail->Password = 'hhsq mldc aree enkx';     // 🔁 Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('mpgayathri001@gmail.com', 'BMMS Motors');
            $mail->addAddress('mpbalanmurugan@gmail.com', 'CEO');

            $mail->isHTML(true);
            $mail->Subject = "Vehicle Expense Report - $chasis_no";

            // ✅ Email body with HTML table
            $mail->Body = "
            <h2 style='color:#2c3e50;'>🚗 Vehicle Expense Details</h2>
            <table border='1' cellspacing='0' cellpadding='8' style='border-collapse:collapse;width:100%;font-family:Arial;'>
                <tr style='background:#f2f2f2;'><th>Chassis No</th><td>$chasis_no</td></tr>
                <tr><th>RTO Amount</th><td>₹" . number_format($rto_amount, 2) . " (Date: $rto_date)</td></tr>
                <tr><th>Insurance Amount</th><td>₹" . number_format($insurance_amount, 2) . " (Date: $insurance_date)</td></tr>
                <tr><th>Vehicle Incoming Date</th><td>$vehicle_incoming_date</td></tr>
            </table>
            <br>
            <h3 style='color:#34495e;'>Additional Expenses:</h3>
            <table border='1' cellspacing='0' cellpadding='8' style='border-collapse:collapse;width:100%;font-family:Arial;'>
                <tr style='background:#f2f2f2;'><th>Description</th><th>Amount</th></tr>
                $extra_table_rows
            </table>
            <br>
            <h3 style='text-align:right;color:#27ae60;'>Final Total: ₹" . number_format($final_total, 2) . "</h3>
            <p style='color:#555;'>Sent automatically from BMMS Motors Vehicle Expense System.</p>
            ";

            $mail->send();
            echo "<script>alert('Vehicle expense saved successfully!'); window.location.href='vehicleexpense.php';</script>";
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
    <title>Vehicle Expense Entry</title>
    <style>
        body { font-family: Arial; background: #f3f4f6; margin: 40px; }
        form { background: #fff; padding: 25px; border-radius: 10px; width: 650px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; }
        .expense-group { display: flex; gap: 10px; margin-bottom: 10px; }
        .expense-group input { flex: 1; }
        button { background: green; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background: darkgreen; }
        h2 { text-align: center; color: #333; }
        .add-btn { background: #007bff; margin-bottom: 10px; }
        .add-btn:hover { background: #0056b3; }
        .total { font-size: 18px; font-weight: bold; color: #444; text-align: right; }
    </style>
</head>
<body>

<h2>Vehicle Expense Entry</h2>

<form method="POST">
    <label>Vehicle Chassis No:</label>
    <input type="text" name="chasis_no" required>

    <label>RTO Amount (₹):</label>
    <input type="number" name="rto_amount" id="rto_amount" oninput="calculateTotal()" required>

    <label>RTO Date:</label>
    <input type="date" name="rto_date" required>

    <label>Insurance Amount (₹):</label>
    <input type="number" name="insurance_amount" id="insurance_amount" oninput="calculateTotal()" required>

    <label>Insurance Date:</label>
    <input type="date" name="insurance_date" required>

    <label>Vehicle Incoming Date:</label>
    <input type="date" name="vehicle_incoming_date" required>

    <div class="extra-expenses">
        <label>Additional Expenses:</label>
        <div id="expenseContainer">
            <div class="expense-group">
                <input type="text" name="desc[]" placeholder="Description">
                <input type="number" name="amount[]" placeholder="Amount (₹)" oninput="calculateTotal()">
            </div>
        </div>
        <button type="button" class="add-btn" onclick="addExpense()">+ Add More</button>
    </div>

    <p class="total">Final Total: ₹<span id="finalTotal">0</span></p>

    <button type="submit">Submit</button>
</form>

<script>
function addExpense() {
    const div = document.createElement('div');
    div.className = 'expense-group';
    div.innerHTML = `
        <input type="text" name="desc[]" placeholder="Description">
        <input type="number" name="amount[]" placeholder="Amount (₹)" oninput="calculateTotal()">
    `;
    document.getElementById('expenseContainer').appendChild(div);
}

function calculateTotal() {
    let rto = parseFloat(document.getElementById('rto_amount').value) || 0;
    let insurance = parseFloat(document.getElementById('insurance_amount').value) || 0;
    let total = rto + insurance;

    document.querySelectorAll('input[name="amount[]"]').forEach(el => {
        total += parseFloat(el.value) || 0;
    });

    document.getElementById('finalTotal').innerText = total.toFixed(2);
}
</script>

</body>
</html>
