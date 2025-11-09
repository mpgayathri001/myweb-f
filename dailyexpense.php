<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $item = $_POST['item'];
    $amount = $_POST['amount'];
    $total = $_POST['total'];

    // Handle multiple image uploads
    $uploaded_images = [];
    if (!empty($_FILES['expense_images']['name'][0])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        foreach ($_FILES['expense_images']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES["expense_images"]["name"][$key]);
            $target_file = $target_dir . time() . "_" . $file_name;
            move_uploaded_file($tmp_name, $target_file);
            $uploaded_images[] = $target_file;
        }
    }

    $image_paths = implode(",", $uploaded_images); // Store all images as comma-separated paths

    // Insert data into DB
    $stmt = $conn->prepare("INSERT INTO daily_expense (date, item_description, amount, total, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdds", $date, $item, $amount, $total, $image_paths);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Daily Expense Added Successfully!'); window.location='dailyexpense.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daily Expense | BMMS Motors</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('admindashboardimage.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      min-height: 100vh;
      padding: 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #ffeb3b;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.5);
    }

    form {
      background: rgba(0,0,0,0.6);
      padding: 30px;
      border-radius: 15px;
      width: 90%;
      max-width: 900px;
      box-shadow: 0 0 15px rgba(0,0,0,0.4);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      color: white;
      margin-bottom: 20px;
    }

    th, td {
      border-bottom: 1px solid rgba(255,255,255,0.2);
      padding: 10px;
      text-align: center;
    }

    th {
      background-color: rgba(255,255,255,0.2);
    }

    input[type="text"], input[type="number"], input[type="date"] {
      width: 100%;
      padding: 8px;
      border: none;
      border-radius: 6px;
      outline: none;
    }

    input[type="file"] {
      color: white;
    }

    button {
      background-color: #ff9800;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 10px;
    }

    button:hover {
      background-color: #ffa726;
      transform: scale(1.05);
    }

    .add-btn {
      background-color: #4caf50;
    }

    .add-btn:hover {
      background-color: #66bb6a;
    }

    .remove-btn {
      background-color: #e53935;
    }

    .remove-btn:hover {
      background-color: #ef5350;
    }

    .total-box {
      margin-top: 15px;
      font-size: 18px;
      font-weight: bold;
      text-align: right;
    }

    .upload-section {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: flex-start;
    }

    .upload-row {
      display: flex;
      align-items: center;
      gap: 10px;
    }
  </style>
</head>
<body>
  <h1>Daily Expense Entry</h1>

  <form method="POST" enctype="multipart/form-data">
    <table id="expenseTable">
      <thead>
        <tr>
          <th>Date</th>
          <th>Item Description</th>
          <th>Amount (₹)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="date" name="date" required></td>
          <td><input type="text" name="item" placeholder="Enter Item" required></td>
          <td><input type="number" name="amount" class="amount" step="0.01" required></td>
          <td><button type="button" class="add-btn" onclick="addRow()">+</button></td>
        </tr>
      </tbody>
    </table>

    <div class="total-box">
      Total: ₹ <span id="totalAmount">0.00</span>
      <input type="hidden" name="total" id="totalInput">
    </div>

    <!-- Image Upload Section -->
    <div class="upload-section" id="imageSection">
      <label>Upload Bill Images:</label>
      <div class="upload-row">
        <input type="file" name="expense_images[]" accept="image/*">
        <button type="button" class="add-btn" onclick="addImage()">+</button>
      </div>
    </div>

    <div style="text-align:center;">
      <button type="submit">Save Expense</button>
    </div>
  </form>

  <script>
    // Add a new expense row
    function addRow() {
      const table = document.getElementById("expenseTable").getElementsByTagName("tbody")[0];
      const newRow = document.createElement("tr");

      newRow.innerHTML = `
        <td><input type="date" name="date" required></td>
        <td><input type="text" name="item" placeholder="Enter Item" required></td>
        <td><input type="number" name="amount" class="amount" step="0.01" required></td>
        <td><button type="button" class="remove-btn" onclick="removeRow(this)">✕</button></td>
      `;
      table.appendChild(newRow);
      updateTotal();
    }

    // Remove a specific row
    function removeRow(btn) {
      btn.parentNode.parentNode.remove();
      updateTotal();
    }

    // Update total when amount fields change
    document.addEventListener('input', function(e) {
      if (e.target.classList.contains('amount')) {
        updateTotal();
      }
    });

    function updateTotal() {
      const amounts = document.querySelectorAll('.amount');
      let total = 0;
      amounts.forEach(a => {
        total += parseFloat(a.value) || 0;
      });
      document.getElementById('totalAmount').innerText = total.toFixed(2);
      document.getElementById('totalInput').value = total.toFixed(2);
    }

    // Add new image upload field
    function addImage() {
      const imageSection = document.getElementById('imageSection');
      const newDiv = document.createElement('div');
      newDiv.classList.add('upload-row');
      newDiv.innerHTML = `
        <input type="file" name="expense_images[]" accept="image/*">
        <button type="button" class="remove-btn" onclick="removeImage(this)">✕</button>
      `;
      imageSection.appendChild(newDiv);
    }

    function removeImage(btn) {
      btn.parentNode.remove();
    }
  </script>
</body>
</html>
