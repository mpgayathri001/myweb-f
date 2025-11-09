<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $added_by = $_SESSION['email'] ?? 'Unknown';

    $date_of_visit = $_POST['date_of_visit'];
    $locations = $_POST['locations'];
    $employees = $_POST['employees'];
    $descriptions = $_POST['description'];
    $amounts = $_POST['amount'];

    // Combine expense details
    $expenses = [];
    $total = 0;
    for ($i = 0; $i < count($descriptions); $i++) {
        $desc = trim($descriptions[$i]);
        $amt = floatval($amounts[$i]);
        if ($desc !== "" && $amt > 0) {
            $expenses[] = ["desc" => $desc, "amt" => $amt];
            $total += $amt;
        }
    }

    // Handle image uploads
    $uploaded_files = [];
    $upload_dir = "uploads/marketing/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES['proof_images']['name'])) {
        foreach ($_FILES['proof_images']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['proof_images']['error'][$index] === UPLOAD_ERR_OK) {
                $fileName = basename($_FILES['proof_images']['name'][$index]);
                $targetPath = $upload_dir . time() . "_" . $fileName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $uploaded_files[] = basename($targetPath);
                }
            }
        }
    }

    $locations_str = implode(", ", $locations);
    $employees_str = implode(", ", $employees);
    $expenses_json = json_encode($expenses);
    $images_str = implode(", ", $uploaded_files);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO marketing_details (date_of_visit, locations, employees, expense_details, total_amount, proof_images, added_by)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", 
        $date_of_visit, 
        $locations_str, 
        $employees_str, 
        $expenses_json, 
        $total, 
        $images_str, 
        $added_by
    );

    if ($stmt->execute()) {
        // ✅ Email sending section
        require 'PHPMailer-master/src/PHPMailer.php';
        require 'PHPMailer-master/src/SMTP.php';
        require 'PHPMailer-master/src/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mpgayathri001@gmail.com';
            $mail->Password = 'hhsq mldc aree enkx';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('mpgayathri001@gmail.com', 'BMMS MOTORS');
            $mail->addAddress('mpbalanmurugan@gmail.com', 'CEO - BMMS Motors');
            $mail->addCC('gayathri.b2162@gmail.com', 'Admin - BMMS Motors');

            $mail->isHTML(true);
            $mail->Subject = "📝 New Marketing Report - $date_of_visit";
            $mail->Body = "
                <h2>Marketing Report - BMMS MOTORS</h2>
                <p><strong>Date of Visit:</strong> $date_of_visit</p>
                <p><strong>Locations:</strong> $locations_str</p>
                <p><strong>Employees:</strong> $employees_str</p>
                <p><strong>Total Expense:</strong> ₹$total</p>
                <p><strong>Added By:</strong> $added_by</p>
                <h3>Expense Details:</h3>
                <ul>";
            foreach ($expenses as $exp) {
                $mail->Body .= "<li>{$exp['desc']} - ₹{$exp['amt']}</li>";
            }
            $mail->Body .= "</ul>";

            foreach ($uploaded_files as $file) {
                $mail->addAttachment("uploads/marketing/$file");
            }

            $mail->send();
            echo "<script>alert('Marketing details saved'); window.location.href='addmarketing.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Details saved, but email could not be sent: {$mail->ErrorInfo}'); window.location.href='addmarketing.php';</script>";
        }
    } else {
        echo "<script>alert('Error saving data: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Marketing | BMMS Motors</title>
<style>
  body {
    margin: 0;
    padding: 0;
    font-family: "Poppins", sans-serif;
    background: url('marketingbackgroundimage.png') no-repeat center center fixed;
    background-size: cover;
    color: white;
  }
  .container {
    max-width: 1000px;
    margin: 120px auto;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(3px);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
  }
  h2 { text-align: center; color: #ffeb3b; margin-bottom: 20px; }
  label { display: block; margin-top: 10px; font-weight: bold; color: #ffeb3b; }
  input, textarea {
    width: 100%; padding: 10px; border: none; border-radius: 6px;
    background: rgba(255,255,255,0.2); color: black; margin-top: 5px;
    font-size: 18px;
  }
  .dynamic-section { margin-top: 10px; }
  .add-btn {
    background: #ff9800; border: none; color: white; padding: 6px 12px;
    border-radius: 5px; cursor: pointer; font-size: 14px; margin-top: 5px;
  }
  .add-btn:hover { background: #e68900; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { border: 1px solid rgba(255,255,255,0.3); padding: 10px; text-align: center; }
  th { background: rgba(0,0,0,0.4); }
  .total {
    text-align: right; margin-top: 15px; font-size: 20px; color: #ffeb3b;
    font-weight: bold;
  }
  .upload-section { margin-top: 20px; }
  .submit-btn {
    background: #ff9800; border: none; color: white; padding: 12px 25px;
    border-radius: 8px; cursor: pointer; font-size: 18px;
    display: block; margin: 25px auto 0; transition: 0.3s;
  }
  .submit-btn:hover { background: #e68900; transform: scale(1.05); }
  footer {
    text-align: center; padding: 15px; color: #ccc; font-size: 14px;
    background: rgba(0,0,0,0.4); margin-top: 30px; border-radius: 0 0 15px 15px;
  }
</style>
</head>
<body>

<div class="container">
  <h2>Marketing Field Details</h2>
  <form action="" method="POST" enctype="multipart/form-data">

    <label>Field Locations</label>
    <div id="locationContainer">
      <div class="dynamic-section">
        <input type="text" name="locations[]" placeholder="Enter location" required>
      </div>
    </div>
    <button type="button" class="add-btn" onclick="addLocation()">+ Add another location</button>

    <label>Date of Visit</label>
    <input type="date" name="date_of_visit" required>

    <label>Work Done By</label>
    <div id="employeeContainer">
      <div class="dynamic-section">
        <input type="text" name="employees[]" placeholder="Enter employee name" required>
      </div>
    </div>
    <button type="button" class="add-btn" onclick="addEmployee()">+ Add another employee</button>

    <label>Expense Details</label>
    <table id="expenseTable">
      <thead>
        <tr>
          <th>Description</th>
          <th>Amount (₹)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="text" name="description[]" placeholder="Enter description" required></td>
          <td><input type="number" name="amount[]" class="amount" placeholder="0" required oninput="calculateTotal()"></td>
          <td><button type="button" class="add-btn" onclick="addExpenseRow()">+</button></td>
        </tr>
      </tbody>
    </table>

    <div class="total">Total: ₹<span id="totalAmount">0</span></div>

    <div class="upload-section">
      <label>Upload Proof Images</label>
      <div id="imageContainer">
        <div class="dynamic-section">
          <input type="file" name="proof_images[]" accept="image/*">
        </div>
      </div>
      <button type="button" class="add-btn" onclick="addImageField()">+ Add another image</button>
    </div>

    <button type="submit" class="submit-btn">Submit Marketing Details</button>
  </form>
</div>

<footer>© 2025 BMMS Motors. All Rights Reserved.</footer>

<script>
  function addLocation() {
    const container = document.getElementById('locationContainer');
    const input = document.createElement('div');
    input.className = 'dynamic-section';
    input.innerHTML = `<input type="text" name="locations[]" placeholder="Enter location">`;
    container.appendChild(input);
  }

  function addEmployee() {
    const container = document.getElementById('employeeContainer');
    const input = document.createElement('div');
    input.className = 'dynamic-section';
    input.innerHTML = `<input type="text" name="employees[]" placeholder="Enter employee name">`;
    container.appendChild(input);
  }

  function addExpenseRow() {
    const table = document.getElementById('expenseTable').querySelector('tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><input type="text" name="description[]" placeholder="Enter description" required></td>
      <td><input type="number" name="amount[]" class="amount" placeholder="0" required oninput="calculateTotal()"></td>
      <td><button type="button" class="add-btn" onclick="removeRow(this)">-</button></td>
    `;
    table.appendChild(row);
  }

  function removeRow(btn) {
    btn.closest('tr').remove();
    calculateTotal();
  }

  function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.amount').forEach(input => {
      total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalAmount').textContent = total.toFixed(2);
  }

  function addImageField() {
    const container = document.getElementById('imageContainer');
    const inputDiv = document.createElement('div');
    inputDiv.className = 'dynamic-section';
    inputDiv.innerHTML = `<input type="file" name="proof_images[]" accept="image/*">`;
    container.appendChild(inputDiv);
  }
</script>

</body>
</html>
