<?php
include 'db.php';

$limit = 20; // 20 records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Handle single row update (AJAX request)
if (isset($_POST['action']) && $_POST['action'] == 'update_row') {
    $id = $_POST['id'];
    $client_name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $reminder_date = mysqli_real_escape_string($conn, $_POST['reminder_date']);
    $reminder_time = mysqli_real_escape_string($conn, $_POST['reminder_time']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update = "UPDATE leads SET client_name='$client_name', location='$location', phone='$phone',
               source='$source', reminder_date='$reminder_date', reminder_time='$reminder_time',
               status='$status' WHERE id='$id'";
    if (mysqli_query($conn, $update)) {
        echo "✅ Saved successfully";
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
    exit;
}

// Pagination
$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM leads");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = mysqli_query($conn, "SELECT * FROM leads ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Leads | BMMS Motors</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #111;
  color: #fff;
}
.container {
  width: 95%;
  margin: 30px auto;
  background: #1e1e1e;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 0 10px #000;
}
h2 {
  color: #ffcc00;
  text-align: center;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}
th, td {
  border: 1px solid #444;
  padding: 6px;
  text-align: center;
}
th {
  background: #222;
  color: #ffcc00;
}
input, select {
  width: 100%;
  background: #222;
  color: #fff;
  border: 1px solid #444;
  border-radius: 5px;
  padding: 5px;
}
button {
  background: #ff9800;
  border: none;
  padding: 5px 10px;
  border-radius: 6px;
  cursor: pointer;
  color: #fff;
  font-weight: bold;
}
button:hover {
  background: #e68900;
}
.pagination {
  margin-top: 20px;
  text-align: center;
}
.pagination a {
  color: #ffcc00;
  background: #222;
  padding: 8px 15px;
  margin: 0 5px;
  border-radius: 5px;
  text-decoration: none;
}
.pagination a.active {
  background: #ff9800;
  color: #fff;
}
.pagination a:hover {
  background: #ffcc00;
  color: #000;
}
.status-msg {
  font-size: 14px;
  color: #00ff90;
}
</style>
<script>
function saveRow(id) {
  const row = document.getElementById('row_' + id);
  const formData = new FormData();
  formData.append('action', 'update_row');
  formData.append('id', id);
  formData.append('client_name', row.querySelector('[name="client_name"]').value);
  formData.append('location', row.querySelector('[name="location"]').value);
  formData.append('phone', row.querySelector('[name="phone"]').value);
  formData.append('source', row.querySelector('[name="source"]').value);
  formData.append('reminder_date', row.querySelector('[name="reminder_date"]').value);
  formData.append('reminder_time', row.querySelector('[name="reminder_time"]').value);
  formData.append('status', row.querySelector('[name="status"]').value);

  fetch('editleads.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    const msg = row.querySelector('.status-msg');
    msg.innerText = data;
    msg.style.color = data.includes('✅') ? '#00ff90' : '#ff4444';
    setTimeout(() => { msg.innerText = ''; }, 2500);
  });
}
</script>
</head>
<body>
<div class="container">
  <h2>📝 Edit Leads - BMMS Motors</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Client Name</th>
        <th>Location</th>
        <th>Phone</th>
        <th>Source</th>
        <th>Reminder Date</th>
        <th>Reminder Time</th>
        <th>Status</th>
        <th>Save</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <tr id="row_<?php echo $row['id']; ?>">
        <td><?php echo $row['id']; ?></td>
        <td><input type="text" name="client_name" value="<?php echo htmlspecialchars($row['client_name']); ?>"></td>
        <td><input type="text" name="location" value="<?php echo htmlspecialchars($row['location']); ?>"></td>
        <td><input type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>"></td>
        <td><input type="text" name="source" value="<?php echo htmlspecialchars($row['source']); ?>"></td>
        <td><input type="date" name="reminder_date" value="<?php echo htmlspecialchars($row['reminder_date']); ?>"></td>
        <td><input type="time" name="reminder_time" value="<?php echo htmlspecialchars($row['reminder_time']); ?>"></td>
        <td>
          <select name="status">
            <option value="Waiting List" <?php if($row['status']=="Waiting List") echo "selected"; ?>>Waiting List</option>
            <option value="Interested" <?php if($row['status']=="Interested") echo "selected"; ?>>Interested</option>
            <option value="Not Interested" <?php if($row['status']=="Not Interested") echo "selected"; ?>>Not Interested</option>
          </select>
        </td>
        <td>
          <button type="button" onclick="saveRow(<?php echo $row['id']; ?>)">💾 Save</button>
          <div class="status-msg"></div>
        </td>
        <td>
          <a href="delete_lead.php?id=<?php echo $row['id']; ?>" style="color:#ff4444;" onclick="return confirm('Delete this lead?');">❌</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?php echo $page - 1; ?>">⬅️ Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
      <a href="?page=<?php echo $page + 1; ?>">Next ➡️</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
