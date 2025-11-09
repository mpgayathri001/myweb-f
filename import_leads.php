<?php
include 'db.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    array_shift($rows); // skip header
}

if (isset($_POST['save_leads'])) {
    foreach ($_POST['client_name'] as $i => $name) {
        if (trim($name) == '') continue;
        $loc = $_POST['location'][$i];
        $phone = $_POST['phone'][$i];
        $source = $_POST['source'][$i];
        $reminder_date = $_POST['reminder_date'][$i];
$reminder_time = $_POST['reminder_time'][$i];
        $status = $_POST['status'][$i];

       $sql = "INSERT INTO leads (client_name, location, phone, source, reminder_date, reminder_time, status)
        VALUES ('$name','$loc','$phone','$source','$reminder_date','$reminder_time','$status')";
        mysqli_query($conn, $sql);
    }
    echo "<script>alert('✅ Leads saved successfully!');window.location='viewleads.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lead Management | BMMS Motors</title>
<style>
body { font-family: 'Poppins', sans-serif; background: #111; color: #fff; }
.container { width: 90%; margin: 30px auto; background: #1e1e1e; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #000; }
h2 { color: #ffcc00; text-align: center; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { border: 1px solid #444; padding: 10px; text-align: center; }
th { background: #222; color: #ffcc00; }
input, select { background: #222; color: #fff; border: 1px solid #444; border-radius: 5px; padding: 6px; }
button { background: #ff9800; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; color: #fff; }
button:hover { background: #e68900; }
.add-btn { background: #333; color: #ffcc00; border: 1px dashed #ffcc00; margin: 10px 0; }
.delete-btn { background: #a00; border: none; color: white; border-radius: 50%; width: 28px; height: 28px; font-weight: bold; }
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
  <h2>📋 Lead Management - BMMS Motors</h2>

  <!-- Excel Import -->
  <form method="POST" enctype="multipart/form-data">
    <label>Import from Excel:</label>
    <input type="file" name="excel_file" accept=".xlsx,.xls">
    <button type="submit">📥 Import</button>
  </form>

  <form method="POST">
    <table id="leadTable">
      <thead>
        <tr>
          <th>Client Name</th>
          <th>Phone No</th>
          <th>Location</th>
          <th>Source</th>
          <th>Remail Date</th>
          <th>Remail Time</th>
          <th>Status</th>
          <th>❌</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)): ?>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><input type="text" name="client_name[]" value="<?php echo $r[0] ?? ''; ?>"></td>
              <td><input type="text" name="location[]" value="<?php echo $r[1] ?? ''; ?>"></td>
              <td><input type="text" name="phone[]" value="<?php echo $r[2] ?? ''; ?>"></td>
              <td>
                <select name="source[]">
                  <option value="Social Media">Social Media</option>
                  <option value="Walk-in">Walk-in</option>
                  <option value="Marketing">Marketing</option>
                  <option value="By Bala Murugan Sir">By Bala Murugan Sir</option>
                  <option value="By Sathis">By Sathis</option>
                  <option value="By Mani">By Mani</option>
                </select>
              </td>
              <td><input type="date" name="reminder_date[]"></td>
              <td><input type="time" name="reminder_time[]"></td>
              <td>
                <select name="status[]">
                  <option value="Interested">Interested</option>
                  <option value="Not Interested">Not Interested</option>
                  <option value="Waiting List">Waiting List</option>
                </select>
              </td>
              <td><button type="button" class="delete-btn" onclick="deleteRow(this)">×</button></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <button type="button" class="add-btn" onclick="addRow()">+ Add Lead</button>
    <br><br>
    <button type="submit" name="save_leads">💾 Save All Leads</button>
  </form>
  <div style="text-align:center;">
    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
  </div>
</div>

<script>
function addRow() {
  const table = document.getElementById('leadTable').getElementsByTagName('tbody')[0];
  const row = document.createElement('tr');
  row.innerHTML = `
    <td><input type="text" name="client_name[]" required></td>
    <td><input type="text" name="location[]" required></td>
    <td><input type="text" name="phone[]" required></td>
    <td>
      <select name="source[]">
        <option value="Social Media">Social Media</option>
        <option value="Walk-in">Walk-in</option>
        <option value="Marketing">Marketing</option>
        <option value="By Bala Murugan Sir">By Bala Murugan Sir</option>
        <option value="By Sathis">By Sathis</option>
        <option value="By Mani">By Mani</option>
      </select>
    </td>
    <td><input type="date" name="reminder_date[]"></td>
    <td><input type="time" name="reminder_time[]"></td>
    <td>
      <select name="status[]">
        <option value="Interested">Interested</option>
        <option value="Not Interested">Not Interested</option>
        <option value="Waiting List">Waiting List</option>
      </select>
    </td>
    <td><button type="button" class="delete-btn" onclick="deleteRow(this)">×</button></td>
  `;
  table.appendChild(row);
}
function deleteRow(btn) {
  btn.parentElement.parentElement.remove();
}
</script>
</body>
</html>
