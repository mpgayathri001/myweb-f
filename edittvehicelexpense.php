<?php
include("db.php");
date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Invalid access! ID missing.");
}

$id = intval($_GET['id']);

// ✅ Fetch existing data
$stmt = $conn->prepare("SELECT * FROM vehicle_expense WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ No record found for ID: $id");
}

$row = $result->fetch_assoc();
$stmt->close();

$extra_expenses = json_decode($row['extra_expenses'], true) ?: [];

// ✅ On form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $chasis_no = $_POST['chasis_no'];
    $rto_amount = floatval($_POST['rto_amount']);
    $rto_date = $_POST['rto_date'];
    $insurance_amount = floatval($_POST['insurance_amount']);
    $insurance_date = $_POST['insurance_date'];
    $vehicle_incoming_date = $_POST['vehicle_incoming_date'];

    $extra_expenses = [];
    if (!empty($_POST['desc'])) {
        for ($i = 0; $i < count($_POST['desc']); $i++) {
            if (!empty($_POST['desc'][$i]) && !empty($_POST['amount'][$i])) {
                $extra_expenses[] = [
                    "desc" => $_POST['desc'][$i],
                    "amount" => floatval($_POST['amount'][$i])
                ];
            }
        }
    }

    $extra_json = json_encode($extra_expenses);
    $total_extra = array_sum(array_column($extra_expenses, 'amount'));
    $total_expense = $rto_amount + $insurance_amount + $total_extra;

    // ✅ Update query
    $update = $conn->prepare("UPDATE vehicle_expense 
        SET chasis_no=?, rto_amount=?, rto_date=?, insurance_amount=?, insurance_date=?, vehicle_incoming_date=?, extra_expenses=?, total_expense=? 
        WHERE id=?");
    $update->bind_param("sdsssssdi", $chasis_no, $rto_amount, $rto_date, $insurance_amount, $insurance_date, $vehicle_incoming_date, $extra_json, $total_expense, $id);

    if ($update->execute()) {
        // ✅ Send Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mpgayathri001@gmail.com'; // your Gmail
            $mail->Password = 'hhsq mldc aree enkx'; // app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('mpgayathri001@gmail.com', 'BMMS Motors');
            $mail->addAddress('mpbalanmurugan@gmail.com', 'CEO');

            $mail->isHTML(true);
            $mail->Subject = "Vehicle Expense Updated - $chasis_no";

            $expense_rows = "";
            foreach ($extra_expenses as $e) {
                $expense_rows .= "<tr><td>{$e['desc']}</td><td>₹{$e['amount']}</td></tr>";
            }

            $mail->Body = "
                <h2>Updated Vehicle Expense Report</h2>
                <table border='1' cellspacing='0' cellpadding='8' style='border-collapse:collapse;width:100%;'>
                    <tr><th>Chasis No</th><td>$chasis_no</td></tr>
                    <tr><th>RTO Amount</th><td>₹$rto_amount</td></tr>
                    <tr><th>RTO Date</th><td>$rto_date</td></tr>
                    <tr><th>Insurance Amount</th><td>₹$insurance_amount</td></tr>
                    <tr><th>Insurance Date</th><td>$insurance_date</td></tr>
                    <tr><th>Vehicle Incoming Date</th><td>$vehicle_incoming_date</td></tr>
                </table>
                <br>
                <h3>Extra Expenses</h3>
                <table border='1' cellspacing='0' cellpadding='8' style='border-collapse:collapse;width:100%;'>
                    <tr><th>Description</th><th>Amount (₹)</th></tr>
                    $expense_rows
                    <tr><td><b>Total Expense</b></td><td><b>₹$total_expense</b></td></tr>
                </table>
                <p style='color:green;'>✅ Record successfully updated in the database.</p>
            ";

            $mail->send();
            echo "<script>alert('Updated successfully!'); window.location.href='editvehicleexpense.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Updated, but email failed: {$mail->ErrorInfo}'); window.location.href='editvehicleexpense.php';</script>";
        }
    } else {
        echo "<script>alert('Update failed: " . $update->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Vehicle Expense</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; margin: 40px; }
        form { background: #fff; padding: 25px; border-radius: 10px; width: 650px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: darkgreen; }
        .expense-row { display: flex; gap: 10px; margin-bottom: 10px; }
        .expense-row input { flex: 1; }
        .add-btn { background: #007bff; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        .remove-btn { background: red; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        h2 { text-align: center; color: #333; }
    </style>
    <script>
        function addRow() {
            const container = document.getElementById('extraContainer');
            const div = document.createElement('div');
            div.classList.add('expense-row');
            div.innerHTML = `
                <input type="text" name="desc[]" placeholder="Description" required>
                <input type="number" name="amount[]" step="0.01" placeholder="Amount" required>
                <button type="button" class="remove-btn" onclick="this.parentNode.remove()">✖</button>`;
            container.appendChild(div);
        }
    </script>
</head>
<body>

<h2>Edit Vehicle Expense</h2>
<form method="POST">
    <label>Chasis No:</label>
    <input type="text" name="chasis_no" value="<?php echo htmlspecialchars($row['chasis_no']); ?>" required>

    <label>RTO Amount (₹):</label>
    <input type="number" name="rto_amount" step="0.01" value="<?php echo htmlspecialchars($row['rto_amount']); ?>" required>

    <label>RTO Date:</label>
    <input type="date" name="rto_date" value="<?php echo htmlspecialchars($row['rto_date']); ?>" required>

    <label>Insurance Amount (₹):</label>
    <input type="number" name="insurance_amount" step="0.01" value="<?php echo htmlspecialchars($row['insurance_amount']); ?>" required>

    <label>Insurance Date:</label>
    <input type="date" name="insurance_date" value="<?php echo htmlspecialchars($row['insurance_date']); ?>" required>

    <label>Vehicle Incoming Date:</label>
    <input type="date" name="vehicle_incoming_date" value="<?php echo htmlspecialchars($row['vehicle_incoming_date']); ?>" required>

    <h3>Extra Expenses</h3>
    <div id="extraContainer">
        <?php foreach ($extra_expenses as $e): ?>
            <div class="expense-row">
                <input type="text" name="desc[]" value="<?php echo htmlspecialchars($e['desc']); ?>" required>
                <input type="number" name="amount[]" step="0.01" value="<?php echo htmlspecialchars($e['amount']); ?>" required>
                <button type="button" class="remove-btn" onclick="this.parentNode.remove()">✖</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="add-btn" onclick="addRow()">+ Add Expense</button><br><br>

    <button type="submit">Update Vehicle Expense</button>
</form>

</body>
</html>
